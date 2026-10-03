<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoginCodeController extends Controller
{
    public function create()
    {
        return view('auth.login-code');
    }

    public function send(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $code = (string) random_int(100000, 999999);

        DB::table('login_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::raw("كود الدخول لـ OrderFlow هو: {$code}\nصالح لمدة 10 دقائق.", function ($message) use ($request) {
            $message->to($request->email)->subject('كود الدخول - OrderFlow');
        });

        return redirect()->route('login.code.verify', ['email' => $request->email])
            ->with('status', 'تم إرسال الكود إلى بريدك الإلكتروني.');
    }

    public function verify(Request $request)
    {
        return view('auth.login-code-verify', ['email' => $request->query('email')]);
    }

    public function attempt(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
        ]);

        $record = DB::table('login_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>=', now())
            ->latest('id')
            ->first();

        if (! $record) {
            return back()->withErrors(['code' => 'الكود غير صحيح أو منتهي الصلاحية.']);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        Auth::login($user, remember: true);

        DB::table('login_codes')->where('email', $request->email)->delete();

        return redirect()->route('dashboard');
    }
}
