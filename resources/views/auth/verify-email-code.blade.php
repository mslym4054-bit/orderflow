<x-guest-layout>
    <div class="text-center">
        <h2 class="text-xl font-bold text-ink">
            تأكيد البريد الإلكتروني
        </h2>

        <p class="mt-2 text-sm text-muted">
            أرسلنا رمز تحقق مكونًا من 6 أرقام إلى بريدك الإلكتروني.
        </p>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-md border border-line bg-base px-4 py-3 text-sm text-neon">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('email.verification.verify') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="code" value="رمز التحقق" />

            <x-text-input
                id="code"
                type="text"
                name="code"
                inputmode="numeric"
                maxlength="6"
                pattern="[0-9]{6}"
                class="mt-1 block w-full text-center tracking-[0.5em]"
                placeholder="000000"
                required
                autofocus
            />

            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            تأكيد البريد الإلكتروني
        </x-primary-button>
    </form>

    <form method="POST" action="{{ route('email.verification.resend') }}" class="mt-4 text-center">
        @csrf

        <button
            type="submit"
            class="text-sm text-neon hover:text-neon-dim"
        >
            إعادة إرسال الرمز
        </button>
    </form>

    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="text-sm text-muted hover:text-ink"
            >
                تسجيل الخروج
            </button>
        </form>
    </div>
</x-guest-layout>
