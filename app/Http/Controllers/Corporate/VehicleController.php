<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vehicle::class);

        $query = Vehicle::query()->latest();

        if (! $request->user()->isSuperAdmin()) {
            $query->where('corporate_id', $request->user()->corporate_id);
        }

        $vehicles = $query->paginate(15);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        $this->authorize('create', Vehicle::class);

        return view('vehicles.form', ['vehicle' => new Vehicle()]);
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::create($request->validated() + [
            'corporate_id' => $request->user()->corporate_id,
            'owner_name' => $request->input('owner_name') ?: $request->user()->corporate?->company_name,
        ]);

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Vehicle registered.');
    }

    public function show(Vehicle $vehicle): View
    {
        $this->authorize('view', $vehicle);

        $vehicle->load(['quotes' => fn ($query) => $query->latest()->limit(5)]);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        $this->authorize('update', $vehicle);

        return view('vehicles.form', compact('vehicle'));
    }

    public function update(StoreVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('update', $vehicle);

        $vehicle->update($request->validated());

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete', $vehicle);

        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('status', 'Vehicle removed.');
    }
}
