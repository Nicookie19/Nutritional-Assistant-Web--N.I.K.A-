<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'admin_code' => ['required', 'string'],
        ]);

        $adminCode = config('app.admin_registration_code', env('ADMIN_REGISTRATION_CODE', '1000808790'));

        if ($request->admin_code !== $adminCode) {
            throw ValidationException::withMessages([
                'admin_code' => 'Invalid admin code.',
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->is_admin) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Access Denied: This portal is restricted to administrator accounts only.',
            ]);
        }

        return redirect()->intended('/admin');
    }

    public function showRegister(): View
    {
        return view('auth.admin-register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'is_admin' => ['accepted'],
            'admin_code' => ['required', 'string', 'max:255'],
        ]);

        $adminCode = config('app.admin_registration_code', env('ADMIN_REGISTRATION_CODE', '1000808790'));

        if ($request->admin_code !== $adminCode) {
            throw ValidationException::withMessages([
                'admin_code' => 'Invalid admin code.',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/admin');
    }
}
