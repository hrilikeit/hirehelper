<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\GetStartedMail;
use App\Mail\VerifyEmailMail;
use App\Models\EmailLog;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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

        // Bot protection: honeypot field (must be empty)
        if ($request->filled('website')) {
            // Silently reject — bots fill hidden fields
            return redirect()->route('workspace.index');
        }

        // Bot protection: form must have been open for at least 3 seconds
        $loadedAt = (int) $request->input('_form_loaded_at', 0);
        if ($loadedAt > 0 && (now()->timestamp - $loadedAt) < 3) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Please wait a moment before submitting.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\']+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree_terms' => ['required', 'accepted'],
        ], [
            'name.regex' => 'Please enter a valid name.',
            'agree_terms.required' => 'You must agree to the Terms of Service and Privacy Policy.',
            'agree_terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
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

        // Record first login time and detect country
        $user->recordLogin($request->ip());

        try {
            if (EmailSetting::isActive('get_started')) {
                $getStartedMail = new GetStartedMail(
                    user: $user,
                    dashboardUrl: route('workspace.dashboard'),
                );
                Mail::to($user->email)->send($getStartedMail);

                EmailLog::record(
                    userId: $user->id,
                    emailType: 'get_started',
                    subject: 'Welcome to HireHelper',
                    toEmail: $user->email,
                    body: $getStartedMail->render(),
                );
            }

            if (EmailSetting::isActive('verify_email')) {
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addHours(24),
                    ['id' => $user->id, 'hash' => sha1($user->email)],
                );

                $verifyMail = new VerifyEmailMail(
                    user: $user,
                    verificationUrl: $verificationUrl,
                );
                Mail::to($user->email)->send($verifyMail);

                EmailLog::record(
                    userId: $user->id,
                    emailType: 'verify_email',
                    subject: 'Verify your email',
                    toEmail: $user->email,
                    body: $verifyMail->render(),
                );
            }
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

        // Record login time and detect country
        Auth::user()->recordLogin($request->ip());

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

        // Support ?redirect=/services/slug for service subscription flow
        $redirect = trim((string) $request->input('redirect', ''));
        if ($redirect !== '' && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return $redirect;
        }

        $next = trim((string) $request->input('next', ''));

        if ($next !== '' && str_starts_with($next, '/') && ! str_starts_with($next, '//')) {
            return $next;
        }

        return null;
    }
}
