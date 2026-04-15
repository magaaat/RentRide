<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Vehicle;
use App\Services\TenantDataMirror;
use Illuminate\Console\Command;

/**
 * Backfill tenant MySQL mirror databases from central data.
 */
class MirrorTenantDataCommand extends Command
{
    protected $signature = 'tenant:mirror-sync {tenant_id? : Optional tenant ID}';

    protected $description = 'Copy central tenant data into each tenant MySQL mirror database';

    public function handle(TenantDataMirror $mirror): int
    {
        if (! $mirror->isEnabled()) {
            $this->warn('Mirroring only runs when DB_CONNECTION=mysql and tenant mirror databases exist.');

            return self::SUCCESS;
        }

        $q = Tenant::query()->where('status', 'approved');
        if ($this->argument('tenant_id')) {
            $q->where('id', (int) $this->argument('tenant_id'));
        }
        $tenants = $q->get();

        foreach ($tenants as $tenant) {
            $db = $mirror->tenantDatabaseName((int) $tenant->id);
            if (! $mirror->tenantDatabaseExists($db)) {
                $this->line("Skip tenant {$tenant->id}: database {$db} not found.");

                continue;
            }

            $this->info("Syncing tenant {$tenant->id} ({$tenant->company_name})…");

            foreach (Vehicle::where('tenant_id', $tenant->id)->cursor() as $vehicle) {
                $mirror->syncVehicle($vehicle);
            }
            foreach (Customer::where('tenant_id', $tenant->id)->cursor() as $customer) {
                $mirror->syncCustomer($customer);
            }
            foreach (Booking::where('tenant_id', $tenant->id)->cursor() as $booking) {
                $mirror->syncBooking($booking);
            }
            foreach (Payment::where('tenant_id', $tenant->id)->cursor() as $payment) {
                $mirror->syncPayment($payment);
            }
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
