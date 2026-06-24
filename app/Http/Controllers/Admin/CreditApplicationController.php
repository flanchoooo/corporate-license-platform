<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditApplication;
use App\Services\CreditApplicationService;
use App\Services\DeliveryService;
use App\Services\LicenseDiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreditApplicationController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-platform');

        $applications = CreditApplication::with(['quote', 'vehicle', 'corporate'])->latest()->paginate(20);

        return view('admin.credit-applications.index', compact('applications'));
    }

    public function show(CreditApplication $application): View
    {
        $this->authorize('manage-platform');

        $application->load(['quote.items', 'vehicle', 'corporate']);

        return view('admin.credit-applications.show', compact('application'));
    }

    public function approve(
        CreditApplication $application,
        Request $request,
        CreditApplicationService $credits,
        LicenseDiskService $disks,
        DeliveryService $deliveries
    ): RedirectResponse {
        $this->authorize('manage-platform');

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application = $credits->approve($application, $validated['admin_notes'] ?? null);
        $disk = $disks->issueFromQuote($application->quote);
        $deliveries->createForQuote($application->quote, [
            'delivery_address' => $application->delivery_address,
            'delivery_mobile' => $application->delivery_mobile,
            'delivery_landmark' => $application->delivery_landmark,
        ], $disk, $application);

        return redirect()->route('admin.credit-applications.show', $application)->with('status', 'Credit approved, disk generated, and delivery order created.');
    }

    public function reject(CreditApplication $application, Request $request, CreditApplicationService $credits): RedirectResponse
    {
        $this->authorize('manage-platform');

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $credits->reject($application, $validated['admin_notes'] ?? null);

        return redirect()->route('admin.credit-applications.show', $application)->with('status', 'Credit application rejected.');
    }
}
