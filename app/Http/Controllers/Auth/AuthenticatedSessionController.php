<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Verified users go directly to the dashboard.
        if ($user->email_verified_at) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Unverified users receive a new verification code.
        $code = (string) random_int(100000, 999999);

        $user->verificationCodes()->delete();

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw(
            "رمز التحقق الخاص بك في OrderFlow هو: {$code}\n\nالرمز صالح لمدة 10 دقائق.",
            function ($message) use ($user) {
                $message
                    ->to($user->email)
                    ->subject('رمز التحقق من البريد الإلكتروني - OrderFlow');
            }
        );

        return redirect()->route('email.verification');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
