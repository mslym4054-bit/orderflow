<x-guest-layout>
    <x-auth-session-status class="mb-4 text-neon" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" value="كلمة المرور" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center gap-2">
            <x-checkbox id="remember_me" name="remember" />
            <label for="remember_me" class="text-sm text-muted">تذكرني</label>
        </div>

        <div class="flex items-center justify-between gap-3">
            @if (Route::has('password.request'))
                <a class="text-sm text-muted hover:text-ink" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
            @endif
            <a href="{{ route('login.code') }}" class="text-sm text-neon hover:text-neon-dim">الدخول بكود عبر الإيميل</a>
            <x-primary-button>دخول</x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="text-sm text-muted text-center pt-2">
                ليس لديك حساب؟
                <a href="{{ route('register') }}" class="text-neon hover:text-neon-dim">سجّل الآن</a>
            </p>
        @endif
    </form>
</x-guest-layout>
