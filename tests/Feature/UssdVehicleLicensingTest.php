<?php

namespace Tests\Feature;

use App\Models\Corporate;
use App\Models\DeliveryOrder;
use App\Models\Quote;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UssdVehicleLicensingTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_returns_vehicle_licensing_menu_in_gateway_contract(): void
    {
        $response = $this->postJson('/ussd-service/api/ussd', $this->payload([
            'stage' => 'START',
            'message' => '',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('transactionID', 'sess-001')
            ->assertJsonPath('sourceNumber', '263772341693')
            ->assertJsonPath('destinationNumber', '586')
            ->assertJsonPath('stage', 'session_active')
            ->assertJsonPath('channel', 'USSD')
            ->assertJsonPath('applicationTransactionID', 'sess-001')
            ->assertJsonPath('transactionType', 'MENU_PROCESSING')
            ->assertJsonPath('back', false)
            ->assertJsonFragment([
                'message' => "Vehicle Licensing\n1. Buy License\n2. Buy License on Credit\n3. View Vehicle Details\n4. Track Mutero Delivery\n\nReply 1, 2, 3, or 4.",
            ]);
    }

    public function test_invalid_menu_selection_keeps_session_active(): void
    {
        $response = $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'message' => '9',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('stage', 'session_active')
            ->assertJsonPath('message', "Invalid option.\n\nVehicle Licensing\n1. Buy License\n2. Buy License on Credit\n3. View Vehicle Details\n4. Track Mutero Delivery\n\nReply 1, 2, 3, or 4.");
    }

    public function test_buy_license_flow_prompts_for_plate_then_insurance_type(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'stage' => 'START',
            'message' => '',
            'transactionID' => 'buy-flow',
        ]));

        $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'message' => '1',
            'transactionID' => 'buy-flow',
        ]))
            ->assertOk()
            ->assertJsonPath('stage', 'session_active')
            ->assertJsonPath('message', 'Please enter the vehicle number plate.');

        $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'message' => 'ABC1234',
            'transactionID' => 'buy-flow',
        ]))
            ->assertOk()
            ->assertJsonPath('stage', 'session_active')
            ->assertJsonPath('message', "Choose insurance type:\n1. Third Party\n2. Full Cover\n\nReply 0 to cancel.");
    }

    public function test_delivery_tracking_can_complete_session(): void
    {
        $order = $this->deliveryOrder();

        $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'stage' => 'START',
            'message' => '',
            'transactionID' => 'track-flow',
        ]));

        $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'message' => '4',
            'transactionID' => 'track-flow',
        ]))
            ->assertOk()
            ->assertJsonPath('message', 'Enter your Mutero delivery reference, order number, license disk reference, plate number, or contact mobile.');

        $this->postJson(route('ussd.vehicle-licensing'), $this->payload([
            'message' => 'MUTERO-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            'transactionID' => 'track-flow',
        ]))
            ->assertOk()
            ->assertJsonPath('stage', 'COMPLETE')
            ->assertJsonFragment([
                'message' => "Mutero Delivery\nReference: MUTERO-".str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)."\nOrder: #{$order->id}\nVehicle: ABC1234\nStatus: Pending\nAddress: 1 Samora Machel Avenue\nContact: 263772341693",
            ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'transactionTime' => '2026-04-21T19:34:00.000+00:00',
            'transactionID' => 'sess-001',
            'sourceNumber' => '263772341693',
            'destinationNumber' => '586',
            'message' => '',
            'stage' => 'session_active',
            'channel' => 'USSD',
        ], $overrides);
    }

    private function deliveryOrder(): DeliveryOrder
    {
        $corporate = Corporate::create([
            'company_name' => 'Demo Logistics Pvt Ltd',
            'registration_number' => 'DEMO-001',
            'tax_number' => 'TIN-DEMO-001',
            'physical_address' => '1 Samora Machel Avenue, Harare',
            'contact_person' => 'Demo Administrator',
            'phone_number' => '263772000000',
            'email' => 'demo@example.com',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $vehicle = Vehicle::create([
            'corporate_id' => $corporate->id,
            'number_plate' => 'ABC1234',
            'engine_capacity' => 1800,
            'make' => 'Toyota',
            'model' => 'Hilux',
            'year' => 2021,
            'vehicle_type' => 'Pickup',
            'fuel_type' => 'Diesel',
            'owner_name' => $corporate->company_name,
            'last_license_expires_at' => now()->addDays(14)->toDateString(),
        ]);

        $quote = Quote::create([
            'corporate_id' => $corporate->id,
            'vehicle_id' => $vehicle->id,
            'quote_number' => 'BOT-TEST-001',
            'status' => 'pending',
            'subtotal_cents' => 10000,
            'total_cents' => 10000,
            'expires_at' => now()->addDays(3),
        ]);

        return DeliveryOrder::create([
            'quote_id' => $quote->id,
            'vehicle_id' => $vehicle->id,
            'delivery_address' => '1 Samora Machel Avenue',
            'contact_mobile' => '263772341693',
            'status' => 'pending',
        ]);
    }
}
