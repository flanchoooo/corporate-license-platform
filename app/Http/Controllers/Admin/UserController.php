<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Corporate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-platform');

        $users = User::with('corporate')->latest()->paginate(20);
        $corporates = Corporate::orderBy('company_name')->get();
        $roles = UserRole::cases();

        return view('admin.users.index', compact('users', 'corporates', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage-platform');

        $validated = $request->validate([
            'corporate_id' => ['nullable', 'exists:corporates,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:super_admin,corporate_admin,corporate_viewer'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'corporate_id' => $validated['role'] === UserRole::SuperAdmin->value ? null : $validated['corporate_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_approved' => true,
            'approved_at' => now(),
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'User created.');
    }
}
