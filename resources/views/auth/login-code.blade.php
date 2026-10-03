<x-guest-layout>
    @if (session('status'))
        <div class="mb-4 text-sm text-neon">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login.code.send') }}" class="space-y-4">
        @csrf
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('login') }}" class="text-sm text-muted hover:text-ink">الدخول بكلمة المرور بدلًا من ذلك</a>
            <x-primary-button>إرسال الكود</x-primary-button>
        </div>
    </form>
</x-guest-layout>
