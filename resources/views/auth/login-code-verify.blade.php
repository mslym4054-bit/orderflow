<x-guest-layout>
    <p class="text-sm text-muted mb-4">تم إرسال كود مكوّن من 6 أرقام إلى {{ $email }}.</p>

    <form method="POST" action="{{ route('login.code.attempt') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <x-input-label for="code" value="الكود" />
            <x-text-input id="code" type="text" name="code" required autofocus inputmode="numeric" maxlength="6" />
            <x-input-error :messages="$errors->get('code')" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>دخول</x-primary-button>
        </div>
    </form>
</x-guest-layout>
