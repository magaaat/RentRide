<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Subscription plans
        SubscriptionPlan::updateOrCreate(
            ['key' => 'basic'],
            [
                'name' => 'Basic',
                'tier' => 'Basic',
                'feature_tier' => 'basic',
                'base_price' => 249,
                'billing_period' => 'month',
                'currency' => 'PHP',
                'discount_type' => 'none',
                'discount_value' => 0,
                'features' => [
                    'Core booking management',
                    'Vehicle listing',
                    'Customer records',
                ],
                'is_active' => true,
                'show_on_landing' => true,
                'sort_order' => 10,
            ]
        );
        SubscriptionPlan::updateOrCreate(
            ['key' => 'standard'],
            [
                'name' => 'Standard',
                'tier' => 'Standard',
                'feature_tier' => 'standard',
                'base_price' => 449,
                'billing_period' => 'month',
                'currency' => 'PHP',
                'discount_type' => 'none',
                'discount_value' => 0,
                'features' => [
                    'Everything in Basic',
                    'Payment tracking',
                    'Sales dashboard',
                ],
                'is_active' => true,
                'show_on_landing' => true,
                'sort_order' => 20,
            ]
        );
        SubscriptionPlan::updateOrCreate(
            ['key' => 'premium'],
            [
                'name' => 'Premium',
                'tier' => 'Premium',
                'feature_tier' => 'premium',
                'base_price' => 699,
                'billing_period' => 'month',
                'currency' => 'PHP',
                'discount_type' => 'none',
                'discount_value' => 0,
                'features' => [
                    'Everything in Standard',
                    'Advanced analytics',
                    'Maintenance tracking',
                    'Auto notifications',
                    'Featured listings',
                ],
                'is_active' => true,
                'show_on_landing' => true,
                'sort_order' => 30,
            ]
        );

        // Create a platform-level super admin (no tenant)
        User::updateOrCreate(
            ['email' => 'superadmin@rentridesa.test'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'tenant_id' => null,
            ]
        );

        $this->call(ModuleSeeder::class);
    }
}
