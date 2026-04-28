<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Mail\CustomerBookingStatusChangedMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PaymentController extends TenantControllerBase
{
    public function index()
    {
        $payments = Payment::with('booking')
            ->where('tenant_id', $this->tenantId())
            ->latest()
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Booking $booking)
    {
        $this->authorizeTenantAccess($booking);

        $booking->loadMissing('vehicle', 'payment');

        return view('payments.create', compact('booking'));
    }

    public function store(StorePaymentRequest $request, Booking $booking)
    {
        $this->authorizeTenantAccess($booking);

        $data = $request->validated();

        $booking->loadMissing('vehicle', 'customer', 'payment', 'tenant');

        DB::transaction(function () use ($data, $booking) {
            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                array_merge($data, [
                    'tenant_id' => $this->tenantId(),
                ])
            );

            $booking->refresh();
            $booking->load('payment');

            if ($data['payment_status'] === 'paid' && $booking->status === 'pending') {
                if ($booking->hasOverlappingConfirmedBooking()) {
                    throw ValidationException::withMessages([
                        'payment_status' => 'Another confirmed booking already overlaps these dates. Resolve the calendar conflict before marking this payment as paid.',
                    ]);
                }

                $previousBookingStatus = $booking->status;

                $booking->update(['status' => 'confirmed']);
                $booking->refresh();

                $vehicle = $booking->vehicle;
                if ($vehicle && $vehicle->status !== 'rented') {
                    $vehicle->update(['status' => 'rented']);
                }

                $tenant = Auth::user()->tenant;
                if ($tenant && $tenant->hasFeature(Tenant::FEATURE_AUTO_NOTIFICATIONS)) {
                    $booking->load('customer', 'vehicle', 'tenant');
                    $email = $booking->customer?->email;
                    if ($email) {
                        try {
                            Mail::to($email)->send(new CustomerBookingStatusChangedMail($booking, $previousBookingStatus));
                        } catch (\Throwable $e) {
                            //
                        }
                    }
                }
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }
}
