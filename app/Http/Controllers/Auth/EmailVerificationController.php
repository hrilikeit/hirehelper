<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * Send a verification email to the authenticated user.
     */
    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return back()->with('info', 'Your email is already verified.');
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        try {
            Mail::to($user->email)->send(new VerifyEmailMail(
                user: $user,
                verificationUrl: $verificationUrl,
            ));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('info', 'Could not send verification email. Please try again later.');
        }

        return back()->with('success', 'Verification email sent! Check your inbox.');
    }

    /**
     * Verify the user's email via signed URL.
     */
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = \App\Models\User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->email))) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        return redirect()
            ->route('workspace.settings')
            ->with('success', 'Your email has been verified!');
    }
}
