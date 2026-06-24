<?php

namespace App\Http\Controllers\Corporate;

use App\Exports\QuotesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateQuoteRequest;
use App\Models\LicenseDisk;
use App\Models\Quote;
use App\Models\Vehicle;
use App\Services\DeliveryService;
use App\Services\QuoteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QuoteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quote::query()->with('vehicle')->latest();

        if (! $request->user()->isSuperAdmin()) {
            $query->where('corporate_id', $request->user()->corporate_id);
        }

        $quotes = $query->paginate(15);

        return view('quotes.index', compact('quotes'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->canWriteCorporateData(), 403);

        $vehicles = Vehicle::query()
            ->when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('corporate_id', $request->user()->corporate_id))
            ->orderBy('number_plate')
            ->get();

        return view('quotes.create', compact('vehicles'));
    }

    public function store(GenerateQuoteRequest $request, QuoteService $quotes): RedirectResponse
    {
        $vehicle = Vehicle::findOrFail($request->integer('vehicle_id'));
        $this->authorize('view', $vehicle);

        $quote = $quotes->createForVehicle(
            $vehicle,
            $request->user(),
            $request->boolean('include_carbon_tax', true),
            $request->input('insurance_type', 'third_party')
        );

        return redirect()->route('quotes.show', $quote)->with('status', 'Quote generated.');
    }

    public function show(Quote $quote): View
    {
        $this->authorize('view', $quote);

        $quote->load(['items', 'vehicle', 'corporate']);

        return view('quotes.show', compact('quote'));
    }

    public function purchase(Quote $quote, Request $request, QuoteService $quotes, DeliveryService $deliveries): RedirectResponse
    {
        $this->authorize('purchase', $quote);

        $validated = $request->validate([
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

        $cardNumber = preg_replace('/\D+/', '', (string) ($validated['card_number'] ?? ''));
        $purchasedQuote = $quotes->purchase($quote, $validated['payment_method'], [
            'source' => 'quote_checkout',
            'user_id' => $request->user()->id,
            'mobile' => $validated['payment_mobile'] ?? null,
            'card_last_four' => $cardNumber !== '' ? substr($cardNumber, -4) : null,
            'card_expiry' => $validated['card_expiry'] ?? null,
        ]);
        $disk = LicenseDisk::where('quote_id', $purchasedQuote->id)->first();
        $deliveries->createForQuote($purchasedQuote, $validated, $disk);

        return redirect()->route('deliveries.index')->with('status', 'License purchased. Delivery order is pending dispatch.');
    }

    public function pdf(Quote $quote)
    {
        $this->authorize('view', $quote);

        $quote->load(['items', 'vehicle', 'corporate']);

        return Pdf::loadView('pdf.quote', compact('quote'))->download($quote->quote_number.'.pdf');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $query = Quote::query()->with('vehicle')->latest();

        if (! $request->user()->isSuperAdmin()) {
            $query->where('corporate_id', $request->user()->corporate_id);
        }

        return Excel::download(new QuotesExport($query->get()), 'quotes.csv');
    }
}
