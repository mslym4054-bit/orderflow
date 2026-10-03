<section>
    <header>
        <h2 class="text-lg font-medium text-ink">معلومات الحساب</h2>
        <p class="mt-1 text-sm text-muted">عدّل اسمك وبريدك الإلكتروني.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="الاسم" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>حفظ</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-neon">تم الحفظ.</p>
            @endif
        </div>
    </form>
</section>
