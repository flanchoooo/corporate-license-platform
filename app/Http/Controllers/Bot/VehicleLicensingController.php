<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Services\AutoQuoteService;
use App\Services\BotSessionService;
use App\Services\CreditApplicationService;
use App\Services\DeliveryService;
use App\Services\LicenseDiskService;
use App\Services\PaymentService;
use App\Services\VehicleLicensingBotService;
use App\Services\VehicleLookupService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VehicleLicensingController extends Controller
{
    private function twiml(string $message): Response
    {
        $xml = '<Response><Message>'.e($message).'</Message></Response>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    public function index(Request $request, BotSessionService $sessions): View
    {
        $botSession = $sessions->current($request);
        $sessions->setState($botSession, 'menu');

        return view('bot.menu');
    }

    public function plate(string $flow, Request $request, BotSessionService $sessions): View
    {
        abort_unless(in_array($flow, ['buy', 'credit', 'details'], true), 404);

        $botSession = $sessions->current($request);
        $sessions->setState($botSession, 'awaiting_plate', ['flow' => $flow]);

        return view('bot.plate', compact('flow'));
    }

    public function quote(Request $request, VehicleLookupService $lookup, AutoQuoteService $quotes, BotSessionService $sessions): View|RedirectResponse
    {
        $validated = $request->validate([
            'flow' => ['required', 'in:buy,credit,details'],
            'number_plate' => ['required', 'string', 'max:30'],
            'insurance_type' => ['nullable', 'in:third_party,full_cover'],
        ]);

        $botSession = $sessions->current($request);
        $sessions->message($botSession, 'customer', 'Plate: '.$validated['number_plate']);

        try {
            $vehicle = $lookup->findByNumberPlate($validated['number_plate']);
        } catch (ModelNotFoundException) {
            return back()->withErrors(['number_plate' => 'Vehicle not found for that number plate.'])->withInput();
        }

        $quote = $quotes->generate($vehicle, $validated['insurance_type'] ?? 'third_party');

        $sessions->setState($botSession, 'quote_generated', [
            'flow' => $validated['flow'],
            'number_plate' => $vehicle->number_plate,
            'quote_id' => $quote->id,
            'insurance_type' => $validated['insurance_type'] ?? 'third_party',
        ]);
        $sessions->message($botSession, 'bot', 'Quote generated', ['quote_id' => $quote->id]);

        return view('bot.quote', [
            'flow' => $validated['flow'],
            'quote' => $quote,
        ]);
    }

    public function trackDeliveryForm(): View
    {
        return view('bot.track-delivery');
    }

    public function trackDelivery(Request $request, VehicleLicensingBotService $bot): View|RedirectResponse
    {
        $validated = $request->validate([
            'tracking_reference' => ['required', 'string', 'max:120'],
        ]);

        $delivery = $bot->findDeliveryOrder($validated['tracking_reference']);

        if (! $delivery) {
            return back()->withErrors(['tracking_reference' => 'No Mutero delivery found for that reference.'])->withInput();
        }

        return view('bot.track-delivery', ['delivery' => $delivery]);
    }

    public function continue(Quote $quote, Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'flow' => ['required', 'in:buy,credit,details'],
            'choice' => ['required', 'in:continue,cancel'],
        ]);

        if ($validated['choice'] === 'cancel') {
            return redirect()->route('bot.menu')->with('status', 'Request cancelled.');
        }

        $quote->load(['items', 'vehicle']);

        return match ($validated['flow']) {
            'buy' => view('bot.buy', compact('quote')),
            'credit' => view('bot.credit', compact('quote')),
            'details' => view('bot.details', compact('quote')),
        };
    }

    public function buy(
        Quote $quote,
        Request $request,
        PaymentService $payments,
        LicenseDiskService $disks,
        DeliveryService $deliveries,
        BotSessionService $sessions
    ): View {
        $validated = $request->validate([
            'delivery_address' => ['required', 'string', 'max:255'],
            'contact_mobile' => ['required', 'string', 'max:40'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:mobile_money,zimswitch,visa,mastercard'],
            'payment_mobile' => ['required_if:payment_method,mobile_money', 'nullable', 'string', 'max:40'],
            'payment_pin' => ['required_if:payment_method,mobile_money', 'nullable', 'string', 'max:8'],
            'card_number' => ['required_if:payment_method,zimswitch,visa,mastercard', 'nullable', 'string', 'max:30'],
            'card_expiry' => ['required_if:payment_method,zimswitch,visa,mastercard', 'nullable', 'string', 'max:10'],
            'card_cvv' => ['required_if:payment_method,zimswitch,visa,mastercard', 'nullable', 'string', 'max:4'],
        ]);

        $quote->load(['items', 'vehicle', 'corporate']);
        $cardNumber = preg_replace('/\D+/', '', (string) ($validated['card_number'] ?? ''));
        $payment = $payments->pay($quote, $validated['payment_method'], [
            'source' => 'vehicle_licensing_bot',
            'contact_mobile' => $validated['contact_mobile'],
            'mobile' => $validated['payment_mobile'] ?? null,
            'card_last_four' => $cardNumber !== '' ? substr($cardNumber, -4) : null,
            'card_expiry' => $validated['card_expiry'] ?? null,
        ]);
        $disk = $disks->issueFromQuote($quote->fresh(['items', 'vehicle', 'corporate']));
        $delivery = $deliveries->createForQuote($quote, $validated, $disk);

        $botSession = $sessions->current($request);
        $sessions->setState($botSession, 'completed', ['quote_id' => $quote->id, 'payment_id' => $payment->id]);
        $sessions->message($botSession, 'bot', 'License purchase completed', ['delivery_order_id' => $delivery->id]);

        return view('bot.done', compact('quote', 'payment', 'disk', 'delivery'));
    }

    public function credit(Quote $quote, Request $request, CreditApplicationService $credits, BotSessionService $sessions): View
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'surname' => ['required', 'string', 'max:120'],
            'national_id' => ['required', 'string', 'max:80'],
            'mobile_number' => ['required', 'string', 'max:40'],
            'address' => ['required', 'string', 'max:255'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_mobile' => ['required', 'string', 'max:40'],
            'delivery_landmark' => ['nullable', 'string', 'max:255'],
        ]);

        $application = $credits->create($quote->load('vehicle'), $validated);

        $botSession = $sessions->current($request);
        $sessions->setState($botSession, 'credit_submitted', ['credit_application_id' => $application->id]);
        $sessions->message($botSession, 'bot', 'Credit application submitted', ['credit_application_id' => $application->id]);

        return view('bot.credit-submitted', compact('application'));
    }

    public function twilio(Request $request, VehicleLicensingBotService $bot): Response
    {
        $validated = $request->validate([
            'From' => ['required', 'string', 'max:80'],
            'To' => ['required', 'string', 'max:80'],
            'Body' => ['nullable', 'string', 'max:1600'],
            'MessageSid' => ['nullable', 'string', 'max:80'],
            'SmsSid' => ['nullable', 'string', 'max:80'],
            'AccountSid' => ['nullable', 'string', 'max:80'],
            'NumMedia' => ['nullable', 'integer', 'min:0'],
        ]);

        $reply = $bot->handle($validated['From'], 'twilio', (string) ($validated['Body'] ?? ''), $validated);
        $message = $reply['message'].($reply['complete'] ? "\n\nReply MENU to start again." : '');

        return $this->twiml($message);
    }

    public function ussd(Request $request, VehicleLicensingBotService $bot): JsonResponse
    {
        $validated = $request->validate([
            'transactionTime' => ['nullable', 'date'],
            'transactionID' => ['required', 'string', 'max:120'],
            'sourceNumber' => ['required', 'string', 'max:80'],
            'destinationNumber' => ['required', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:1600'],
            'stage' => ['required', 'in:START,session_active'],
            'channel' => ['required', 'in:USSD'],
        ]);

        $message = $validated['stage'] === 'START' ? '' : (string) ($validated['message'] ?? '');
        $reply = $bot->handle($validated['transactionID'], 'ussd', $message, $validated);

        return response()->json([
            'transactionTime' => now()->toJSON(),
            'transactionID' => $validated['transactionID'],
            'sourceNumber' => $validated['sourceNumber'],
            'destinationNumber' => $validated['destinationNumber'],
            'message' => $reply['message'],
            'stage' => $reply['complete'] ? 'COMPLETE' : 'session_active',
            'channel' => 'USSD',
            'applicationTransactionID' => $validated['transactionID'],
            'transactionType' => 'MENU_PROCESSING',
            'back' => false,
        ]);
    }
}
