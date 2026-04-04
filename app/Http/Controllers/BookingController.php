<?php

namespace App\Http\Controllers;

use App\Mail\CustomerBookingStatusChangedMail;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\Vehicle;
use App\Support\PlanLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends TenantControllerBase
{
    public function index()
    {
        $bookings = Booking::with(['vehicle', 'customer'])
            ->where('tenant_id', $this->tenantId())
            ->latest()
            ->paginate(20);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $tenantId = $this->tenantId();
        $vehicles = Vehicle::where('tenant_id', $tenantId)
            ->where('status', 'available')
            ->get();
        $customers = Customer::where('tenant_id', $tenantId)->get();

        return view('bookings.create', compact('vehicles', 'customers'));
    }

    public function store(Request $request)
    {
        $tenantId = $this->tenantId();

        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::where('tenant_id', $tenantId)->findOrFail($data['vehicle_id']);

        $overlap = Booking::where('tenant_id', $tenantId)
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

        Booking::create([
            'tenant_id' => $tenantId,
            'vehicle_id' => $vehicle->id,
            'customer_id' => $data['customer_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking created.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $this->authorizeTenantAccess($booking);

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        if ($data['status'] === 'confirmed') {
            $hasConfirmedOverlap = Booking::query()
                ->where('tenant_id', $booking->tenant_id)
                ->where('vehicle_id', $booking->vehicle_id)
                ->where('status', 'confirmed')
                ->where('id', '!=', $booking->id)
                ->where(function ($q) use ($booking) {
                    $q->whereBetween('start_date', [$booking->start_date, $booking->end_date])
                        ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date])
                        ->orWhere(function ($inner) use ($booking) {
                            $inner->where('start_date', '<=', $booking->start_date)
                                ->where('end_date', '>=', $booking->end_date);
                        });
                })
                ->exists();

            if ($hasConfirmedOverlap) {
                return back()->withErrors([
                    'status' => 'Cannot confirm this booking because another confirmed booking overlaps the same vehicle dates.',
                ]);
            }
        }

        $previousStatus = $booking->status;
        $booking->update(['status' => $data['status']]);
        $booking->refresh();

        $vehicle = $booking->vehicle;
        if ($vehicle) {
            if ($booking->status === 'confirmed') {
                if ($vehicle->status !== 'rented') {
                    $vehicle->update(['status' => 'rented']);
                }
            } elseif ($previousStatus === 'confirmed') {
                $hasAnyConfirmed = Booking::query()
                    ->where('tenant_id', $booking->tenant_id)
                    ->where('vehicle_id', $booking->vehicle_id)
                    ->where('status', 'confirmed')
                    ->exists();

                if (! $hasAnyConfirmed && $vehicle->status === 'rented') {
                    $vehicle->update(['status' => 'available']);
                }
            }
        }

        $tenant = Auth::user()->tenant;
        if (
            $tenant
            && $tenant->hasFeature(Tenant::FEATURE_AUTO_NOTIFICATIONS)
            && $previousStatus !== $data['status']
        ) {
            $booking->load('customer', 'vehicle', 'tenant');
            $email = $booking->customer?->email;
            if ($email) {
                try {
                    Mail::to($email)->send(new CustomerBookingStatusChangedMail($booking, $previousStatus));
                } catch (\Throwable $e) {
                    // optional in local dev
                }
            }
        }

        return back()->with('success', 'Booking status updated.');
    }
}

