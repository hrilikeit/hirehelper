<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\GetStartedMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClientAuthController extends Controller
{
    public function showRegisterForm(Request $request)
    {
        $next = $this->rememberNextUrl($request);

        if (Auth::check()) {
            return $next
                ? redirect()->to($next)
                : redirect()->route('workspace.dashboard');
        }

        return view('auth.client-register', [
            'next' => $next,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $next = $this->rememberNextUrl($request);

        if (Auth::check()) {
            return $next
                ? redirect()->to($next)
                : redirect()->route('workspace.dashboard');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'client',
            'password' => Hash::make($data['password']),
            'notify_messages' => true,
            'notify_reports' => true,
            'reminder_frequency' => 'weekly',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        try {
            Mail::to($user->email)->send(new GetStartedMail(
                user: $user,
                dashboardUrl: route('workspace.dashboard'),
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        if ($next) {
            $request->session()->put('url.intended', $next);
        }

        return redirect()
            ->intended(route('workspace.welcome'))
            ->with('success', 'Your client workspace is ready.');
    }

    public function showLoginForm(Request $request)
    {
        $next = $this->rememberNextUrl($request);

        if (Auth::check()) {
            return $next
                ? redirect()->to($next)
                : redirect()->route('workspace.dashboard');
        }

        return view('auth.client-login', [
            'next' => $next,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $next = $this->rememberNextUrl($request);

        if (Auth::check()) {
            return $next
                ? redirect()->to($next)
                : redirect()->route('workspace.dashboard');
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

        if ($next) {
            $request->session()->put('url.intended', $next);
        }

        return redirect()
            ->intended(route('workspace.dashboard'))
            ->with('success', 'Welcome back.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('workspace.index')
            ->with('success', 'You have been signed out.');
    }

    protected function rememberNextUrl(Request $request): ?string
    {
        $next = $this->resolveNextUrl($request);

        if ($next) {
            $request->session()->put('url.intended', $next);
        }

        return $next;
    }

    protected function resolveNextUrl(Request $request): ?string
    {
        if ($request->filled('freelancer') && is_numeric($request->input('freelancer'))) {
            return route('workspace.hire-flow', ['freelancer' => (int) $request->input('freelancer')], false);
        }

        $next = trim((string) $request->input('next', ''));

        if ($next !== '' && str_starts_with($next, '/') && ! str_starts_with($next, '//')) {
            return $next;
        }

        return null;
    }
}
