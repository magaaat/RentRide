<?php

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateBookingIds = DB::table('payments')
            ->select('booking_id')
            ->groupBy('booking_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('booking_id');

        foreach ($duplicateBookingIds as $bookingId) {
            $keep = Payment::query()->where('booking_id', $bookingId)->orderByDesc('id')->first();
            if ($keep) {
                Payment::query()->where('booking_id', $bookingId)->where('id', '!=', $keep->id)->delete();
            }
        }

        Booking::query()
            ->doesntHave('payment')
            ->with('vehicle')
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    Payment::create([
                        'tenant_id' => $booking->tenant_id,
                        'booking_id' => $booking->id,
                        'amount' => $booking->calculateTotalAmount(),
                        'payment_method' => 'pending',
                        'payment_status' => 'pending',
                    ]);
                }
            });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['booking_id']);
        });
    }
};
