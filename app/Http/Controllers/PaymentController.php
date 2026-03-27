<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

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

        return view('payments.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $this->authorizeTenantAccess($booking);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_date' => 'nullable|date',
        ]);

        $data['tenant_id'] = $this->tenantId();
        $data['booking_id'] = $booking->id;

        Payment::create($data);

        if ($data['payment_status'] === 'paid') {
            $booking->update(['status' => 'confirmed']);
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }
}

