<?php

namespace App\Http\Controllers\Corporate;

use App\Exports\ImportErrorsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUploadRequest;
use App\Models\VehicleImport;
use App\Services\BulkVehicleImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BulkUploadController extends Controller
{
    public function create(): View
    {
        abort_unless(request()->user()->canWriteCorporateData(), 403);

        $imports = VehicleImport::where('corporate_id', request()->user()->corporate_id)->latest()->limit(10)->get();

        return view('bulk.upload', compact('imports'));
    }

    public function store(BulkUploadRequest $request, BulkVehicleImportService $imports): RedirectResponse
    {
        $import = $imports->import($request->user()->corporate, $request->user()->id, $request->file('file'));

        return redirect()->route('bulk.imports.show', $import)->with('status', 'Vehicle import processed.');
    }

    public function show(VehicleImport $import): View
    {
        abort_unless(request()->user()->isSuperAdmin() || request()->user()->corporate_id === $import->corporate_id, 403);

        $import->load('errors');

        return view('bulk.show', compact('import'));
    }

    public function errors(VehicleImport $import): BinaryFileResponse
    {
        abort_unless(request()->user()->isSuperAdmin() || request()->user()->corporate_id === $import->corporate_id, 403);

        $import->load('errors');

        return Excel::download(new ImportErrorsExport($import), 'vehicle-import-errors.csv');
    }
}
