<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientAuthController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('workspace.dashboard');
        }

        return view('auth.client-register');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('workspace.dashboard');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'role' => 'client',
            'password' => Hash::make($data['password']),
            'notify_messages' => true,
            'notify_reports' => true,
            'reminder_frequency' => 'weekly',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('workspace.welcome')
            ->with('success', 'Your client workspace is ready.');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('workspace.dashboard');
        }

        return view('auth.client-login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('workspace.dashboard');
        }

        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (! Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], (bool) ($data['remember'] ?? false))) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'We could not sign you in with those details.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('workspace.dashboard'))
            ->with('success', 'Welcome back.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('workspace.index')
            ->with('success', 'You have been signed out.');
    }
}
