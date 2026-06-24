<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = DeliveryOrder::with(['quote', 'vehicle', 'rider', 'licenseDisk'])->latest();

        if (! $request->user()->isSuperAdmin()) {
            $query->whereHas('quote', fn ($quote) => $quote->where('corporate_id', $request->user()->corporate_id));
        }

        $deliveryOrders = $query->paginate(15);

        return view('delivery-orders.index', compact('deliveryOrders'));
    }

    public function show(Request $request, DeliveryOrder $deliveryOrder): View
    {
        $deliveryOrder->load(['quote', 'vehicle', 'rider', 'licenseDisk']);

        abort_unless(
            $request->user()->isSuperAdmin() || $request->user()->corporate_id === $deliveryOrder->quote?->corporate_id,
            403
        );

        return view('delivery-orders.show', compact('deliveryOrder'));
    }
}
