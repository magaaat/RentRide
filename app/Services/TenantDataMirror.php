<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use DateTimeInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Mirrors tenant-scoped rows from the central DB into each tenant's own database
 * (created on tenant approval: tenant_{id} in MySQL).
 *
 * The main app always writes to the central database first; this service copies
 * the same rows so tools inspecting tenant_* DBs see the data.
 */
class TenantDataMirror
{
    protected const CONNECTION = 'tenant_data_mirror';

    public function __construct()
    {
    }

    public function isEnabled(): bool
    {
        return config('database.default') === 'mysql';
    }

    public function tenantDatabaseName(int $tenantId): string
    {
        return 'tenant_'.$tenantId;
    }

    public function tenantDatabaseExists(string $databaseName): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        try {
            $rows = DB::select(
                'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ? LIMIT 1',
                [$databaseName]
            );

            return count($rows) > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @return \Illuminate\Database\Connection|null
     */
    public function connection(int $tenantId)
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $dbName = $this->tenantDatabaseName($tenantId);
        if (! $this->tenantDatabaseExists($dbName)) {
            return null;
        }

        $base = config('database.connections.mysql', []);
        Config::set('database.connections.'.self::CONNECTION, array_merge($base, [
            'database' => $dbName,
        ]));
        DB::purge(self::CONNECTION);

        return DB::connection(self::CONNECTION);
    }

    public function syncVehicle(Vehicle $vehicle): void
    {
        $conn = $this->connection((int) $vehicle->tenant_id);
        if (! $conn) {
            return;
        }

        $this->withForeignKeysOff($conn, function () use ($conn, $vehicle) {
            $conn->table('vehicles')->updateOrInsert(
                ['id' => $vehicle->id],
                $this->normalizeRow($vehicle)
            );
        });
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        $conn = $this->connection((int) $vehicle->tenant_id);
        if (! $conn) {
            return;
        }

        try {
            $conn->table('vehicles')->where('id', $vehicle->id)->delete();
        } catch (Throwable $e) {
            Log::debug('TenantDataMirror: deleteVehicle failed', ['e' => $e->getMessage()]);
        }
    }

    public function syncCustomer(Customer $customer): void
    {
        $conn = $this->connection((int) $customer->tenant_id);
        if (! $conn) {
            return;
        }

        $this->withForeignKeysOff($conn, function () use ($conn, $customer) {
            $conn->table('customers')->updateOrInsert(
                ['id' => $customer->id],
                $this->normalizeRow($customer)
            );
        });
    }

    public function deleteCustomer(Customer $customer): void
    {
        $conn = $this->connection((int) $customer->tenant_id);
        if (! $conn) {
            return;
        }

        try {
            $conn->table('customers')->where('id', $customer->id)->delete();
        } catch (Throwable $e) {
            Log::debug('TenantDataMirror: deleteCustomer failed', ['e' => $e->getMessage()]);
        }
    }

    public function syncBooking(Booking $booking): void
    {
        $conn = $this->connection((int) $booking->tenant_id);
        if (! $conn) {
            return;
        }

        $this->withForeignKeysOff($conn, function () use ($conn, $booking) {
            $conn->table('bookings')->updateOrInsert(
                ['id' => $booking->id],
                $this->normalizeRow($booking)
            );
        });
    }

    public function deleteBooking(Booking $booking): void
    {
        $conn = $this->connection((int) $booking->tenant_id);
        if (! $conn) {
            return;
        }

        try {
            $conn->table('bookings')->where('id', $booking->id)->delete();
        } catch (Throwable $e) {
            Log::debug('TenantDataMirror: deleteBooking failed', ['e' => $e->getMessage()]);
        }
    }

    public function syncPayment(Payment $payment): void
    {
        $conn = $this->connection((int) $payment->tenant_id);
        if (! $conn) {
            return;
        }

        $this->withForeignKeysOff($conn, function () use ($conn, $payment) {
            $conn->table('payments')->updateOrInsert(
                ['id' => $payment->id],
                $this->normalizeRow($payment)
            );
        });
    }

    public function deletePayment(Payment $payment): void
    {
        $conn = $this->connection((int) $payment->tenant_id);
        if (! $conn) {
            return;
        }

        try {
            $conn->table('payments')->where('id', $payment->id)->delete();
        } catch (Throwable $e) {
            Log::debug('TenantDataMirror: deletePayment failed', ['e' => $e->getMessage()]);
        }
    }

    public function syncUser(User $user): void
    {
        if (! $user->tenant_id) {
            return;
        }

        $conn = $this->connection((int) $user->tenant_id);
        if (! $conn) {
            return;
        }

        $this->withForeignKeysOff($conn, function () use ($conn, $user) {
            $conn->table('users')->updateOrInsert(
                ['id' => $user->id],
                $this->normalizeRow($user)
            );
        });
    }

    public function deleteUser(User $user): void
    {
        if (! $user->tenant_id) {
            return;
        }

        $conn = $this->connection((int) $user->tenant_id);
        if (! $conn) {
            return;
        }

        try {
            $conn->table('users')->where('id', $user->id)->delete();
        } catch (Throwable $e) {
            Log::debug('TenantDataMirror: deleteUser failed', ['e' => $e->getMessage()]);
        }
    }

    protected function withForeignKeysOff($conn, callable $callback): void
    {
        try {
            $conn->statement('SET FOREIGN_KEY_CHECKS=0');
            $callback();
        } catch (Throwable $e) {
            Log::debug('TenantDataMirror: sync failed', ['e' => $e->getMessage()]);
        } finally {
            try {
                $conn->statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (Throwable $e) {
                // ignore
            }
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function normalizeRow($model): array
    {
        $row = [];
        foreach ($model->getAttributes() as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $row[$key] = $value->format('Y-m-d H:i:s');
            } else {
                $row[$key] = $value;
            }
        }

        return $row;
    }
}
