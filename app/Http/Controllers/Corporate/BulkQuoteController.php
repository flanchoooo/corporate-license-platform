<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\LicenseDisk;
use App\Models\Quote;
use App\Models\Vehicle;
use App\Services\DeliveryService;
use App\Services\QuoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkQuoteController extends Controller
{
    public function create(Request $request): View
    {
        abort_unless($request->user()->canWriteCorporateData(), 403);

        $vehicles = Vehicle::query()
            ->when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('corporate_id', $request->user()->corporate_id))
            ->orderBy('number_plate')
            ->get();

        return view('quotes.bulk', compact('vehicles'));
    }

    public function store(Request $request, QuoteService $quotes): RedirectResponse
    {
        abort_unless($request->user()->canWriteCorporateData(), 403);

        $validated = $request->validate([
            'vehicle_ids' => ['required', 'array', 'min:1'],
            'vehicle_ids.*' => ['integer', 'exists:vehicles,id'],
            'include_carbon_tax' => ['nullable', 'boolean'],
            'insurance_type' => ['nullable', 'in:third_party,full_cover'],
        ]);

        $vehicles = Vehicle::query()
            ->when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('corporate_id', $request->user()->corporate_id))
            ->whereIn('id', $validated['vehicle_ids'])
            ->get();

        $created = 0;

        foreach ($vehicles as $vehicle) {
            $quotes->createForVehicle(
                $vehicle,
                $request->user(),
                $request->boolean('include_carbon_tax', true),
                $validated['insurance_type'] ?? 'third_party'
            );
            $created++;
        }

        return redirect()->route('quotes.index')->with('status', $created.' quotes generated.');
    }

    public function purchase(Request $request, QuoteService $quoteService, DeliveryService $deliveries): RedirectResponse
    {
        abort_unless($request->user()->canWriteCorporateData(), 403);

        $validated = $request->validate([
            'quote_ids' => ['required', 'array', 'min:1'],
            'quote_ids.*' => ['integer', 'exists:quotes,id'],
            'payment_method' => ['required', 'in:mobile_money,zimswitch,visa,mastercard'],
            'payment_mobile' => ['required_if:payment_method,mobile_money', 'nullable', 'string', 'max:40'],
            'payment_pin' => ['required_if:payment_method,mobile_money', 'nullable', 'string', 'max:8'],
            'card_number' => ['required_if:payment_method,zimswitch,visa,mastercard', 'nullable', 'string', 'max:30'],
            'card_expiry' => ['required_if:payment_method,zimswitch,visa,mastercard', 'nullable', 'string', 'max:10'],
            'card_cvv' => ['required_if:payment_method,zimswitch,visa,mastercard', 'nullable', 'string', 'max:4'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'contact_mobile' => ['required', 'string', 'max:40'],
            'landmark' => ['nullable', 'string', 'max:255'],
        ]);

        $quotes = Quote::query()
            ->when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('corporate_id', $request->user()->corporate_id))
            ->where('status', 'pending')
            ->whereIn('id', $validated['quote_ids'])
            ->get();

        $purchased = 0;
        $cardNumber = preg_replace('/\D+/', '', (string) ($validated['card_number'] ?? ''));

        foreach ($quotes as $quote) {
            $purchasedQuote = $quoteService->purchase($quote, $validated['payment_method'], [
                'source' => 'bulk_quote_checkout',
                'user_id' => $request->user()->id,
                'mobile' => $validated['payment_mobile'] ?? null,
                'card_last_four' => $cardNumber !== '' ? substr($cardNumber, -4) : null,
                'card_expiry' => $validated['card_expiry'] ?? null,
            ]);
            $disk = LicenseDisk::where('quote_id', $purchasedQuote->id)->first();
            $deliveries->createForQuote($purchasedQuote, $validated, $disk);
            $purchased++;
        }

        return redirect()->route('deliveries.index')->with('status', $purchased.' delivery orders are pending dispatch.');
    }
}
