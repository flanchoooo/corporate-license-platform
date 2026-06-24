<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\LicenseDisk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LicenseDiskController extends Controller
{
    public function index(Request $request): View
    {
        $query = LicenseDisk::with(['vehicle', 'quote'])->latest();

        if (! $request->user()->isSuperAdmin()) {
            $query->where('corporate_id', $request->user()->corporate_id);
        }

        $licenseDisks = $query->paginate(15);

        return view('license-disks.index', compact('licenseDisks'));
    }

    public function show(LicenseDisk $licenseDisk): View
    {
        abort_unless(request()->user()->isSuperAdmin() || request()->user()->corporate_id === $licenseDisk->corporate_id, 403);

        $licenseDisk->load(['corporate', 'vehicle', 'quote.items']);

        return view('license-disks.show', compact('licenseDisk'));
    }

    public function pdf(LicenseDisk $licenseDisk)
    {
        abort_unless(request()->user()->isSuperAdmin() || request()->user()->corporate_id === $licenseDisk->corporate_id, 403);

        $licenseDisk->load(['corporate', 'vehicle', 'quote.items']);
        $qrCode = base64_encode(QrCode::format('svg')->size(120)->generate($licenseDisk->qr_payload));

        return Pdf::loadView('pdf.license-disk', compact('licenseDisk', 'qrCode'))->download($licenseDisk->reference_number.'.pdf');
    }

    public function verify(string $reference): View
    {
        $licenseDisk = LicenseDisk::with(['corporate', 'vehicle'])->where('reference_number', $reference)->firstOrFail();

        return view('license-disks.verify', compact('licenseDisk'));
    }
}
