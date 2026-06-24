<?php

namespace App\Services;

use App\Models\PricingRule;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class PricingService
{
    public function quoteItems(Vehicle $vehicle, bool $includeCarbonTax = true, string $insuranceType = 'third_party'): Collection
    {
        $insuranceType = $this->normalizeInsuranceType($insuranceType);
        $insuranceAmount = $this->insuranceAmount($vehicle, $insuranceType);
        $insuranceLabel = $insuranceType === 'full_cover' ? 'Full Cover' : 'Third Party';

        $items = collect([
            $this->line('Radio License', 'radio_license', $this->amountFor('radio_license', $vehicle, $this->defaultRadioLicense($vehicle))),
            $this->line('Motor Insurance - '.$insuranceLabel, 'motor_insurance', $insuranceAmount),
            $this->line('ZINARA License', 'zinara_license', $this->amountFor('zinara_license', $vehicle, $this->defaultZinara($vehicle))),
            $this->line('Administration Fee', 'administration_fee', $this->amountFor('administration_fee', $vehicle, 1500)),
            $this->line('Outstanding Arrears', 'arrears', 0),
        ]);

        if ($includeCarbonTax) {
            $items->splice(3, 0, [$this->line('Carbon Tax', 'carbon_tax', $this->amountFor('carbon_tax', $vehicle, $this->defaultCarbonTax($vehicle)))]);
        }

        return $items;
    }

    public function totalFor(Vehicle $vehicle, bool $includeCarbonTax = true, string $insuranceType = 'third_party'): int
    {
        return $this->quoteItems($vehicle, $includeCarbonTax, $insuranceType)->sum('amountCents');
    }

    public function amountFor(string $feeType, Vehicle $vehicle, int $fallback): int
    {
        $rule = PricingRule::query()
            ->where('fee_type', $feeType)
            ->where('is_active', true)
            ->where(function ($query) use ($vehicle) {
                $query->whereNull('min_cc')->orWhere('min_cc', '<=', $vehicle->engine_capacity);
            })
            ->where(function ($query) use ($vehicle) {
                $query->whereNull('max_cc')->orWhere('max_cc', '>=', $vehicle->engine_capacity);
            })
            ->orderByDesc('min_cc')
            ->first();

        return $rule ? (int) $rule->amount_cents : $fallback;
    }

    private function line(string $description, string $feeType, int $amountCents): array
    {
        return [
            'description' => $description,
            'feeType' => $feeType,
            'amountCents' => $amountCents,
            'fee_type' => $feeType,
            'amount_cents' => $amountCents,
        ];
    }

    private function defaultRadioLicense(Vehicle $vehicle): int
    {
        return match (true) {
            $vehicle->engine_capacity <= 1500 => 3000,
            $vehicle->engine_capacity <= 3000 => 4500,
            default => 6000,
        };
    }

    private function defaultInsurance(Vehicle $vehicle): int
    {
        return match (true) {
            $vehicle->engine_capacity <= 1500 => 12000,
            $vehicle->engine_capacity <= 3000 => 18000,
            default => 25000,
        };
    }

    private function insuranceAmount(Vehicle $vehicle, string $insuranceType): int
    {
        $thirdPartyAmount = $this->amountFor('motor_insurance', $vehicle, $this->defaultInsurance($vehicle));

        return $insuranceType === 'full_cover' ? $thirdPartyAmount * 2 : $thirdPartyAmount;
    }

    private function normalizeInsuranceType(string $insuranceType): string
    {
        return in_array($insuranceType, ['third_party', 'full_cover'], true) ? $insuranceType : 'third_party';
    }

    private function defaultZinara(Vehicle $vehicle): int
    {
        return match (true) {
            $vehicle->engine_capacity <= 1500 => 2500,
            $vehicle->engine_capacity <= 3000 => 5000,
            default => 10000,
        };
    }

    private function defaultCarbonTax(Vehicle $vehicle): int
    {
        return match (true) {
            strtolower((string) $vehicle->fuel_type) === 'diesel' => 5000,
            $vehicle->engine_capacity > 3000 => 7500,
            default => 2500,
        };
    }
}
