<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\User;
use App\Services\DeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryOrderController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-platform');

        $orders = DeliveryOrder::with(['quote', 'vehicle', 'rider'])->latest()->paginate(20);
        $riders = User::where('is_approved', true)->orderBy('name')->get();

        return view('admin.delivery-orders.index', compact('orders', 'riders'));
    }

    public function assign(DeliveryOrder $order, Request $request, DeliveryService $deliveries): RedirectResponse
    {
        $this->authorize('manage-platform');

        $validated = $request->validate([
            'rider_user_id' => ['required', 'exists:users,id'],
        ]);

        $deliveries->assign($order, (int) $validated['rider_user_id']);

        return back()->with('status', 'Bike dispatched and marked in transit.');
    }

    public function status(DeliveryOrder $order, Request $request, DeliveryService $deliveries): RedirectResponse
    {
        $this->authorize('manage-platform');

        $validated = $request->validate([
            'status' => ['required', 'in:pending,assigned,in_transit,delivered,failed'],
        ]);

        $deliveries->updateStatus($order, $validated['status']);

        return back()->with('status', 'Delivery status updated.');
    }
}
