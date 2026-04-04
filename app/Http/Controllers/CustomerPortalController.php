<?php

namespace App\Http\Controllers;

use App\Mail\CustomerBookingPlacedMail;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\Vehicle;
use App\Support\PlanLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CustomerPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $tenants = Tenant::where('status', 'approved')
            ->where('is_domain_active', true)
            ->orderBy('company_name')
            ->get();

        $bookings = Booking::query()
            ->with(['vehicle', 'tenant', 'payment', 'customer'])
            ->whereHas('customer', fn ($q) => $q->where('email', $user->email))
            ->latest()
            ->get();

        $activeBookings = $bookings->whereIn('status', ['pending', 'confirmed']);
        $history = $bookings->whereIn('status', ['completed', 'cancelled']);

        return view('customer.dashboard', compact('user', 'tenants', 'activeBookings', 'history'));
    }

    public function tenantsIndex(Request $request)
    {
        $q = Tenant::where('status', 'approved')
            ->where('is_domain_active', true)
            ->orderBy('company_name');

        if ($request->filled('location')) {
            $loc = $request->input('location');
            $q->where(function ($query) use ($loc) {
                $query->where('address', 'like', '%'.$loc.'%')
                    ->orWhere('company_name', 'like', '%'.$loc.'%');
            });
        }

        $tenants = $q->paginate(12)->withQueryString();

        return view('customer.tenants-index', compact('tenants'));
    }

    public function vehicles(Request $request, Tenant $tenant)
    {
        $this->ensureApprovedTenant($tenant);

        $vehicles = Vehicle::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', '!=', 'inactive');

        if ($request->filled('vehicle_type')) {
            $vehicles->where('vehicle_type', 'like', '%'.$request->input('vehicle_type').'%');
        }

        if ($request->filled('min_price')) {
            $vehicles->where('price_per_day', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $vehicles->where('price_per_day', '<=', (float) $request->input('max_price'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->date('start_date');
            $end = $request->date('end_date');
            $busyVehicleIds = Booking::query()
                ->where('tenant_id', $tenant->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($inner) use ($start, $end) {
                            $inner->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->pluck('vehicle_id')
                ->unique()
                ->filter();

            if ($busyVehicleIds->isNotEmpty()) {
                $vehicles->whereNotIn('id', $busyVehicleIds);
            }
        }

        if ($request->boolean('available_only')) {
            $vehicles->where('status', 'available');
        }

        $vehicles = $vehicles->orderBy('vehicle_name')->paginate(12)->withQueryString();

        $vehicleTypes = Vehicle::where('tenant_id', $tenant->id)
            ->select('vehicle_type')
            ->distinct()
            ->orderBy('vehicle_type')
            ->pluck('vehicle_type');

        return view('customer.tenant-vehicles', compact('tenant', 'vehicles', 'vehicleTypes'));
    }

    public function vehicleShow(Tenant $tenant, Vehicle $vehicle)
    {
        $this->ensureApprovedTenant($tenant);
        abort_unless((int) $vehicle->tenant_id === (int) $tenant->id, 404);

        $user = Auth::user();
        $user?->refresh();
        $canRent = $user?->hasDriverLicensePhotos() ?? false;

        return view('customer.vehicle-show', compact('tenant', 'vehicle', 'canRent'));
    }

    public function storeBooking(Request $request, Tenant $tenant, Vehicle $vehicle)
    {
        $this->ensureApprovedTenant($tenant);
        abort_unless((int) $vehicle->tenant_id === (int) $tenant->id, 404);

        $user = Auth::user();
        $user?->refresh();

        if (! $user?->hasDriverLicensePhotos()) {
            return redirect()
                ->route('customer.profile')
                ->withErrors(['license' => 'Upload the front and back of your driver’s license in your profile before booking.']);
        }

        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        if (! in_array($vehicle->status, ['available'], true)) {
            return back()->withErrors(['vehicle' => 'This vehicle is not available for booking right now.'])->withInput();
        }

        $overlap = Booking::where('tenant_id', $tenant->id)
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['date' => 'This vehicle is already booked for the selected dates.'])
                ->withInput();
        }

        $plan = $tenant->subscription_plan ?? 'basic';
        if (! PlanLimits::canCreateBooking($plan, $tenant->id)) {
            return back()
                ->withErrors([
                    'plan' => 'This rental company has reached its monthly booking limit on the Basic plan. Please try again next month or choose another company.',
                ])
                ->withInput();
        }

        $customer = Customer::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'email' => $user->email,
            ],
            [
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address,
            ]
        );

        $customer->update([
            'name' => $user->name,
            'phone' => $user->phone ?? $customer->phone,
            'address' => $user->address ?? $customer->address,
        ]);

        $booking = Booking::create([
            'tenant_id' => $tenant->id,
            'vehicle_id' => $vehicle->id,
            'customer_id' => $customer->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'pending',
        ]);

        if (! empty($user->email)) {
            try {
                Mail::to($user->email)->send(new CustomerBookingPlacedMail($booking->load(['vehicle', 'tenant'])));
            } catch (\Throwable $e) {
                // Mail optional for local dev
            }
        }

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Reservation submitted! You will receive a confirmation email. The rental company will confirm your booking soon.');
    }

    public function search(Request $request)
    {
        $query = Vehicle::query()
            ->with('tenant')
            ->whereIn('status', ['available', 'rented'])
            ->whereHas('tenant', function ($q) {
                $q->where('status', 'approved')->where('is_domain_active', true);
            });

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', 'like', '%'.$request->input('vehicle_type').'%');
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', (float) $request->input('max_price'));
        }

        if ($request->filled('location')) {
            $loc = $request->input('location');
            $query->whereHas('tenant', function ($q) use ($loc) {
                $q->where('address', 'like', '%'.$loc.'%')
                    ->orWhere('company_name', 'like', '%'.$loc.'%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->date('start_date');
            $end = $request->date('end_date');
            $query->whereDoesntHave('bookings', function ($q) use ($start, $end) {
                $q->whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($inner) use ($start, $end) {
                        $inner->whereBetween('start_date', [$start, $end])
                            ->orWhereBetween('end_date', [$start, $end])
                            ->orWhere(function ($x) use ($start, $end) {
                                $x->where('start_date', '<=', $start)
                                    ->where('end_date', '>=', $end);
                            });
                    });
            });
        }

        if ($request->boolean('bookable_only')) {
            $query->where('status', 'available');
        }

        $vehicles = $query->orderBy('price_per_day')->paginate(12)->withQueryString();

        return view('customer.search', compact('vehicles'));
    }

    protected function ensureApprovedTenant(Tenant $tenant): void
    {
        abort_unless($tenant->status === 'approved' && $tenant->is_domain_active, 404);
    }
}
