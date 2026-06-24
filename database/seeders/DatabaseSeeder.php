<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Corporate;
use App\Models\PricingRule;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Platform Super Admin',
                'role' => UserRole::SuperAdmin->value,
                'is_approved' => true,
                'approved_at' => now(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $rules = [
            ['name' => 'Radio License up to 1500cc', 'fee_type' => 'radio_license', 'min_cc' => null, 'max_cc' => 1500, 'amount_cents' => 3000],
            ['name' => 'Radio License 1501-3000cc', 'fee_type' => 'radio_license', 'min_cc' => 1501, 'max_cc' => 3000, 'amount_cents' => 4500],
            ['name' => 'Radio License above 3000cc', 'fee_type' => 'radio_license', 'min_cc' => 3001, 'max_cc' => null, 'amount_cents' => 6000],
            ['name' => 'Motor Insurance up to 1500cc', 'fee_type' => 'motor_insurance', 'min_cc' => null, 'max_cc' => 1500, 'amount_cents' => 12000],
            ['name' => 'Motor Insurance 1501-3000cc', 'fee_type' => 'motor_insurance', 'min_cc' => 1501, 'max_cc' => 3000, 'amount_cents' => 18000],
            ['name' => 'Motor Insurance above 3000cc', 'fee_type' => 'motor_insurance', 'min_cc' => 3001, 'max_cc' => null, 'amount_cents' => 25000],
            ['name' => 'ZINARA up to 1500cc', 'fee_type' => 'zinara_license', 'min_cc' => null, 'max_cc' => 1500, 'amount_cents' => 2500],
            ['name' => 'ZINARA 1501-3000cc', 'fee_type' => 'zinara_license', 'min_cc' => 1501, 'max_cc' => 3000, 'amount_cents' => 5000],
            ['name' => 'ZINARA above 3000cc', 'fee_type' => 'zinara_license', 'min_cc' => 3001, 'max_cc' => null, 'amount_cents' => 10000],
            ['name' => 'Carbon Tax standard', 'fee_type' => 'carbon_tax', 'min_cc' => null, 'max_cc' => null, 'amount_cents' => 2500],
            ['name' => 'Administration Fee', 'fee_type' => 'administration_fee', 'min_cc' => null, 'max_cc' => null, 'amount_cents' => 1500],
            ['name' => 'Bike Delivery Fee', 'fee_type' => 'delivery_fee', 'min_cc' => null, 'max_cc' => null, 'amount_cents' => 500],
        ];

        foreach ($rules as $rule) {
            PricingRule::updateOrCreate(
                ['name' => $rule['name'], 'fee_type' => $rule['fee_type']],
                $rule + ['is_active' => true]
            );
        }

        $corporate = Corporate::updateOrCreate(
            ['registration_number' => 'DEMO-001'],
            [
                'company_name' => 'Demo Logistics Pvt Ltd',
                'tax_number' => 'TIN-DEMO-001',
                'physical_address' => '1 Samora Machel Avenue, Harare',
                'contact_person' => 'Demo Administrator',
                'phone_number' => '263772000000',
                'email' => 'demo@example.com',
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        $corporate->wallet()->firstOrCreate(['corporate_id' => $corporate->id], ['balance_cents' => 1000000]);

        Vehicle::updateOrCreate(
            ['number_plate' => 'ABC1234'],
            [
                'corporate_id' => $corporate->id,
                'engine_capacity' => 1800,
                'make' => 'Toyota',
                'model' => 'Hilux',
                'year' => 2021,
                'vehicle_type' => 'Pickup',
                'fuel_type' => 'Diesel',
                'owner_name' => $corporate->company_name,
                'last_license_expires_at' => now()->addDays(14)->toDateString(),
            ]
        );
    }
}
