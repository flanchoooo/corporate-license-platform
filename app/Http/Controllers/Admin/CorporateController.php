<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Corporate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CorporateController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('manage-platform');

        $corporates = Corporate::query()
            ->withCount(['users', 'vehicles'])
            ->latest()
            ->paginate(15);

        return view('admin.corporates.index', compact('corporates'));
    }

    public function approve(Corporate $corporate): RedirectResponse
    {
        $this->authorize('manage-platform');

        $corporate->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $corporate->users()
            ->where('role', UserRole::CorporateAdmin->value)
            ->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);

        $corporate->wallet()->firstOrCreate();

        return back()->with('status', 'Corporate registration approved.');
    }
}
