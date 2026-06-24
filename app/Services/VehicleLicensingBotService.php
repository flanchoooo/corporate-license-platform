<?php

namespace App\Services;

use App\Models\BotSession;
use App\Models\DeliveryOrder;
use App\Models\Quote;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleLicensingBotService
{
    public function __construct(
        private readonly VehicleLookupService $lookup,
        private readonly AutoQuoteService $quotes,
        private readonly PaymentService $payments,
        private readonly LicenseDiskService $disks,
        private readonly DeliveryService $deliveries,
        private readonly CreditApplicationService $credits,
    ) {
    }

    public function menu(): string
    {
        return "Vehicle Licensing\n1. Buy License\n2. Buy License on Credit\n3. View Vehicle Details\n4. Track Mutero Delivery\n\nReply 1, 2, 3, or 4.";
    }

    public function handle(string $sessionKey, string $channel, string $message, array $payload = []): array
    {
        $body = trim($message);
        $normalizedBody = strtolower($body);
        $session = BotSession::firstOrCreate(
            ['session_key' => "{$channel}:{$sessionKey}"],
            ['channel' => $channel, 'state' => 'menu']
        );
        $context = $session->context ?? [];

        $session->messages()->create([
            'sender' => 'customer',
            'message' => $body,
            'payload' => $payload,
        ]);

        if ($body === '' || in_array($normalizedBody, ['menu', 'hi', 'hello', 'start', '0', 'cancel'], true)) {
            $session->update(['state' => 'menu', 'current_menu' => null, 'number_plate' => null, 'context' => []]);

            return $this->active($this->menu());
        }

        if ($session->state === 'menu') {
            $flow = match ($body) {
                '1' => 'buy',
                '2' => 'credit',
                '3' => 'details',
                '4' => 'track_delivery',
                default => null,
            };

            if (! $flow) {
                return $this->active("Invalid option.\n\n".$this->menu());
            }

            $session->update([
                'state' => $flow === 'track_delivery' ? 'awaiting_delivery_tracking' : 'awaiting_plate',
                'current_menu' => $flow,
                'context' => ['flow' => $flow],
            ]);

            if ($flow === 'track_delivery') {
                return $this->active('Enter your Mutero delivery reference, order number, license disk reference, plate number, or contact mobile.');
            }

            return $this->active('Please enter the vehicle number plate.');
        }

        if ($session->state === 'awaiting_delivery_tracking') {
            $delivery = $this->findDeliveryOrder($body);

            if (! $delivery) {
                return $this->active("No Mutero delivery found for {$body}. Please check the reference and try again, or reply 0 to cancel.");
            }

            $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

            return $this->complete($this->deliveryStatusText($delivery));
        }

        if ($session->state === 'awaiting_plate') {
            try {
                $vehicle = $this->lookup->findByNumberPlate($body);
            } catch (ModelNotFoundException) {
                return $this->active("Vehicle not found for plate {$body}. Please check the number plate and try again, or reply 0 to cancel.");
            }

            $session->update([
                'state' => 'awaiting_insurance_type',
                'number_plate' => $vehicle->number_plate,
                'context' => array_merge($context, [
                    'number_plate' => $vehicle->number_plate,
                ]),
            ]);

            return $this->active("Choose insurance type:\n1. Third Party\n2. Full Cover\n\nReply 0 to cancel.");
        }

        if ($session->state === 'awaiting_insurance_type') {
            $insuranceType = match ($body) {
                '1' => 'third_party',
                '2' => 'full_cover',
                default => null,
            };

            if (! $insuranceType) {
                return $this->active("Invalid insurance option.\n1. Third Party\n2. Full Cover\n\nReply 0 to cancel.");
            }

            try {
                $vehicle = $this->lookup->findByNumberPlate($context['number_plate'] ?? $session->number_plate);
            } catch (ModelNotFoundException) {
                $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

                return $this->active("Vehicle not found. Please reply MENU to start again.");
            }

            $quote = $this->quotes->generate($vehicle, $insuranceType);
            $session->update([
                'state' => 'quote_confirm',
                'number_plate' => $vehicle->number_plate,
                'context' => array_merge($context, [
                    'quote_id' => $quote->id,
                    'number_plate' => $vehicle->number_plate,
                    'insurance_type' => $insuranceType,
                ]),
            ]);

            return $this->active($this->quoteText($quote)."\n\n1. Continue\n2. Cancel");
        }

        if ($session->state === 'quote_confirm') {
            if ($body === '2') {
                $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

                return $this->active("Request cancelled.\n\n".$this->menu());
            }

            if ($body !== '1') {
                return $this->active("Please reply 1 to continue or 2 to cancel.");
            }

            $flow = $context['flow'] ?? $session->current_menu;

            if ($flow === 'details') {
                $quote = Quote::with(['items', 'vehicle', 'corporate'])->findOrFail($context['quote_id']);
                $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

                return $this->complete($this->quoteText($quote));
            }

            if ($flow === 'buy') {
                $session->update(['state' => 'awaiting_payment_method']);

                return $this->active("Choose payment method:\n1. Mobile Money\n2. Zimswitch card\n3. Visa\n4. Mastercard\n\nReply 0 to cancel.");
            }

            if ($flow === 'credit') {
                $session->update(['state' => 'awaiting_credit_kyc']);

                return $this->active("Send KYC details separated by | as:\nName | Surname | National ID | Mobile | Address | Delivery Address | Delivery Mobile | Landmark\n\nReply 0 to cancel.");
            }
        }

        if ($session->state === 'awaiting_payment_method') {
            $paymentMethod = match ($body) {
                '1' => 'mobile_money',
                '2' => 'zimswitch',
                '3' => 'visa',
                '4' => 'mastercard',
                default => null,
            };

            if (! $paymentMethod) {
                return $this->active("Invalid payment option.\n1. Mobile Money\n2. Zimswitch card\n3. Visa\n4. Mastercard\n\nReply 0 to cancel.");
            }

            $session->update([
                'state' => 'awaiting_delivery',
                'context' => array_merge($context, ['payment_method' => $paymentMethod]),
            ]);

            return $this->active("Send delivery details separated by | as:\nDelivery Address | Contact Mobile | Landmark\n\nReply 0 to cancel.");
        }

        if ($session->state === 'awaiting_delivery') {
            $parts = array_map('trim', explode('|', $body));

            if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
                return $this->active("Invalid delivery details. Send:\nDelivery Address | Contact Mobile | Landmark\n\nReply 0 to cancel.");
            }

            $quote = Quote::with(['items', 'vehicle', 'corporate'])->findOrFail($context['quote_id']);
            $payment = $this->payments->pay($quote, $context['payment_method'] ?? 'mobile_money', ['source' => $channel]);
            $disk = $this->disks->issueFromQuote($quote->fresh(['items', 'vehicle', 'corporate']));
            $delivery = $this->deliveries->createForQuote($quote, [
                'delivery_address' => $parts[0],
                'contact_mobile' => $parts[1],
                'landmark' => $parts[2] ?? null,
            ], $disk);

            $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

            return $this->complete("License purchased successfully.\nPayment: {$payment->reference}\nDisk: {$disk->reference_number}\nMutero Delivery: ".$this->muteroReference($delivery)."\nDelivery Order: #{$delivery->id}\nStatus: {$delivery->status}");
        }

        if ($session->state === 'awaiting_credit_kyc') {
            $parts = array_map('trim', explode('|', $body));

            if (count($parts) < 7) {
                return $this->active("Invalid KYC details. Send:\nName | Surname | National ID | Mobile | Address | Delivery Address | Delivery Mobile | Landmark\n\nReply 0 to cancel.");
            }

            $quote = Quote::with(['vehicle'])->findOrFail($context['quote_id']);
            $application = $this->credits->create($quote, [
                'name' => $parts[0],
                'surname' => $parts[1],
                'national_id' => $parts[2],
                'mobile_number' => $parts[3],
                'address' => $parts[4],
                'delivery_address' => $parts[5],
                'delivery_mobile' => $parts[6],
                'delivery_landmark' => $parts[7] ?? null,
            ]);

            $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

            return $this->complete("Credit application submitted.\nApplication: #{$application->id}\nStatus: Pending admin approval.");
        }

        $session->update(['state' => 'menu', 'current_menu' => null, 'context' => []]);

        return $this->active($this->menu());
    }

    public function muteroReference(DeliveryOrder $order): string
    {
        return 'MUTERO-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
    }

    public function findDeliveryOrder(string $trackingReference): ?DeliveryOrder
    {
        $reference = trim($trackingReference);
        $normalized = strtoupper(str_replace(['#', ' '], '', $reference));

        return DeliveryOrder::with(['vehicle', 'quote', 'licenseDisk', 'rider'])
            ->when(preg_match('/^(?:MUTERO-?)?0*(\d+)$/', $normalized, $matches), function ($query) use ($matches) {
                $query->orWhere('id', (int) $matches[1]);
            })
            ->orWhereHas('licenseDisk', fn ($query) => $query->where('reference_number', $reference))
            ->orWhereHas('quote', fn ($query) => $query->where('quote_number', $reference))
            ->orWhereHas('vehicle', fn ($query) => $query->where('number_plate', strtoupper($reference)))
            ->orWhere('contact_mobile', $reference)
            ->latest()
            ->first();
    }

    public function deliveryStatusText(DeliveryOrder $order): string
    {
        return implode("\n", array_filter([
            'Mutero Delivery',
            'Reference: '.$this->muteroReference($order),
            'Order: #'.$order->id,
            'Vehicle: '.$order->vehicle?->number_plate,
            'Status: '.ucwords(str_replace('_', ' ', $order->status)),
            'Address: '.$order->delivery_address,
            'Contact: '.$order->contact_mobile,
            $order->rider ? 'Rider: '.$order->rider->name : null,
            $order->assigned_at ? 'Assigned: '.$order->assigned_at->format('d M Y H:i') : null,
            $order->delivered_at ? 'Delivered: '.$order->delivered_at->format('d M Y H:i') : null,
            $order->failed_at ? 'Failed: '.$order->failed_at->format('d M Y H:i') : null,
        ]));
    }

    public function quoteText(Quote $quote): string
    {
        $quote->loadMissing(['items', 'vehicle', 'corporate']);

        $amount = fn (string $type) => (int) ($quote->items->firstWhere('fee_type', $type)?->amount_cents ?? 0);
        $money = fn (int $cents) => 'USD '.number_format($cents / 100, 2);
        $insuranceItem = $quote->items->firstWhere('fee_type', 'motor_insurance');

        return implode("\n", [
            'Vehicle Quote',
            'Plate: '.$quote->vehicle->number_plate,
            'Owner: '.($quote->vehicle->owner_name ?: $quote->corporate->company_name),
            'Vehicle: '.$quote->vehicle->make.' '.$quote->vehicle->model,
            'Engine: '.number_format($quote->vehicle->engine_capacity).' CC',
            'Expiry: '.(optional($quote->vehicle->last_license_expires_at)->format('d M Y') ?? 'Not set'),
            'ZINARA: '.$money($amount('zinara_license')),
            'Radio: '.$money($amount('radio_license')),
            ($insuranceItem?->description ?? 'Insurance').': '.$money($amount('motor_insurance')),
            'Arrears: '.$money($amount('arrears')),
            'Delivery: '.$money($amount('delivery_fee')),
            'Total: '.$money($quote->total_cents),
        ]);
    }

    private function active(string $message): array
    {
        return ['message' => $message, 'complete' => false];
    }

    private function complete(string $message): array
    {
        return ['message' => $message, 'complete' => true];
    }
}
