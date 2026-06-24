<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Corporate;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'registration_number' => ['required', 'string', 'max:100', 'unique:corporates,registration_number'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'physical_address' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:40'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $corporate = Corporate::create([
            'company_name' => $request->company_name,
            'registration_number' => $request->registration_number,
            'tax_number' => $request->tax_number,
            'physical_address' => $request->physical_address,
            'contact_person' => $request->contact_person,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'status' => 'pending',
        ]);

        $corporate->wallet()->create();

        $user = User::create([
            'corporate_id' => $corporate->id,
            'name' => $request->contact_person,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => UserRole::CorporateAdmin->value,
            'is_approved' => false,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Registration received. Please wait for administrator approval before logging in.');
    }
}
