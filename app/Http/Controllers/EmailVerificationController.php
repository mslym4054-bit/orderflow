<?php

namespace App\Http\Controllers;

use App\Models\EmailVerificationCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\BrevoMailer;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification page.
     */
    public function show(Request $request): View
    {
       return view('auth.verify-email-code');
    }

    /**
     * Send a new verification code.
     */
    public function sendCode(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $code = (string) random_int(100000, 999999);

        $user->verificationCodes()->delete();

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);


$sent = app(BrevoMailer::class)->sendVerificationCode($user, $code);

        if (!$sent) {
            return redirect()
                ->route('email.verification')
                ->with('status', 'تعذر إرسال رمز التحقق. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('email.verification')
            ->with('status', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.');

    }

    /**
     * Verify the submitted code.
     */
    public function verify(Request $request): RedirectResponse
    {

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $verificationCode = $user->verificationCodes()
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$verificationCode) {

            return back()->withErrors([
                'code' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
            ]);
        }

        $user->update([
            'email_verified_at' => now(),
        ]);

        $user->verificationCodes()->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'تم التحقق من بريدك الإلكتروني بنجاح.');
    }
}
