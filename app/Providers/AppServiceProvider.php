<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\TenantDataMirror;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $mirror = $this->app->make(TenantDataMirror::class);

        Vehicle::saved(static function (Vehicle $vehicle) use ($mirror) {
            $mirror->syncVehicle($vehicle);
        });
        Vehicle::deleted(static function (Vehicle $vehicle) use ($mirror) {
            $mirror->deleteVehicle($vehicle);
        });

        Customer::saved(static function (Customer $customer) use ($mirror) {
            $mirror->syncCustomer($customer);
        });
        Customer::deleted(static function (Customer $customer) use ($mirror) {
            $mirror->deleteCustomer($customer);
        });

        Booking::saved(static function (Booking $booking) use ($mirror) {
            $mirror->syncBooking($booking);
        });
        Booking::deleted(static function (Booking $booking) use ($mirror) {
            $mirror->deleteBooking($booking);
        });

        Payment::saved(static function (Payment $payment) use ($mirror) {
            $mirror->syncPayment($payment);
        });
        Payment::deleted(static function (Payment $payment) use ($mirror) {
            $mirror->deletePayment($payment);
        });

        User::saved(static function (User $user) use ($mirror) {
            if ($user->tenant_id) {
                $mirror->syncUser($user);
            }
        });
        User::deleted(static function (User $user) use ($mirror) {
            if ($user->tenant_id) {
                $mirror->deleteUser($user);
            }
        });
    }
}
