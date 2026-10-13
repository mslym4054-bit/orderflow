
<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6"
         dir="{{ $user->locale === 'en' ? 'ltr' : 'rtl' }}"
         x-data="{
            selectedColor: @js(old('brand_color', $user->brand_color ?? 'green')),
            colors: {
                green: '#10b981',
                blue: '#3b82f6',
                purple: '#8b5cf6',
                orange: '#f97316',
                rose: '#f43f5e',
                teal: '#14b8a6',
                indigo: '#6366f1',
                amber: '#f59e0b'
            }
         }">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $user->locale === 'en' ? 'Settings' : 'الإعدادات' }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                {{ $user->locale === 'en'
                    ? 'Personalize your OrderFlow experience.'
                    : 'خصص تجربة استخدامك لنظام OrderFlow.' }}
            </p>
        </div>


@if (session('success'))
    <div
        class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950"
        style="color: #166534 !important;"
        role="status"
    >
        <span style="color: #166534 !important;" class="dark:!text-green-200">
            {{ session('success') }}
        </span>
    </div>
@endif


        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}"
              class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Language --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $user->locale === 'en' ? 'Language' : 'اللغة' }}
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->locale === 'en'
                        ? 'Choose your preferred interface language.'
                        : 'اختر لغة واجهة النظام.' }}
                </p>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <input type="radio" name="locale" value="ar"
                               @checked(old('locale', $user->locale) === 'ar')>
                        <span class="text-gray-900 dark:text-white">العربية</span>
                    </label>

                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <input type="radio" name="locale" value="en"
                            required
                            @checked(old('locale', $user->locale) === 'en')>
                        <span class="text-gray-900 dark:text-white">English</span>
                    </label>
                </div>
            </section>

            {{-- Appearance --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $user->locale === 'en' ? 'Appearance' : 'المظهر' }}
                </h2>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <input type="radio" name="theme" value="light"
                               @checked(old('theme', $user->theme) === 'light')>
                        <span class="text-gray-900 dark:text-white">
                            {{ $user->locale === 'en' ? 'Light mode' : 'الوضع الفاتح' }}
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <input type="radio" name="theme" value="dark"
                               @checked(old('theme', $user->theme) === 'dark')>
                        <span class="text-gray-900 dark:text-white">
                         <span class="text-gray-900 dark:text-white">
                                {{ $user->locale === 'en' ? 'Dark mode' : 'الوضع الداكن' }}
                        </span>
                        </span>
                    </label>
                </div>
            </section>

            {{-- Brand identity --}}
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $user->locale === 'en' ? 'Brand identity' : 'الهوية البصرية' }}
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->locale === 'en'
                        ? 'Choose a color and preview your selection before saving.'
                        : 'اختر اللون وشاهد معاينة له قبل حفظ الإعدادات.' }}
                </p>

                @php
                    $colors = [
                        'green' => ['label' => 'أخضر', 'en' => 'Green', 'hex' => '#10b981'],
                        'blue' => ['label' => 'أزرق', 'en' => 'Blue', 'hex' => '#3b82f6'],
                        'purple' => ['label' => 'بنفسجي', 'en' => 'Purple', 'hex' => '#8b5cf6'],
                        'orange' => ['label' => 'برتقالي', 'en' => 'Orange', 'hex' => '#f97316'],
                        'rose' => ['label' => 'وردي', 'en' => 'Rose', 'hex' => '#f43f5e'],
                        'teal' => ['label' => 'فيروزي', 'en' => 'Teal', 'hex' => '#14b8a6'],
                        'indigo' => ['label' => 'نيلي', 'en' => 'Indigo', 'hex' => '#6366f1'],
                        'amber' => ['label' => 'كهرماني', 'en' => 'Amber', 'hex' => '#f59e0b'],
                    ];
                @endphp

                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($colors as $key => $color)
                        <label
                            class="relative flex cursor-pointer items-center gap-3 rounded-xl border-2 p-3 transition-all duration-200 hover:shadow-md"
                            :style="selectedColor === '{{ $key }}'
                                ? 'border-color: {{ $color['hex'] }}; background-color: {{ $color['hex'] }}12'
                                : ''"
                            :class="selectedColor === '{{ $key }}'
                                ? 'shadow-sm'
                                : 'border-gray-200 dark:border-gray-700'">

                            <input
                                type="radio"
                                name="brand_color"
                                value="{{ $key }}"
                                x-model="selectedColor"
                                @checked(old('brand_color', $user->brand_color) === $key)
                                class="sr-only">

                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full ring-offset-2 transition-all"
                                :style="'background-color: {{ $color['hex'] }}; ' +
                                    (selectedColor === '{{ $key }}'
                                        ? 'outline: 2px solid {{ $color['hex'] }}; outline-offset: 2px'
                                        : '')">

                                <svg
                                    x-show="selectedColor === '{{ $key }}'"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="white"
                                    class="h-4 w-4">
                                    <path fill-rule="evenodd"
                                          d="M16.704 5.29a1 1 0 010 1.414l-7.1 7.1a1 1 0 01-1.415 0l-3.1-3.1a1 1 0 011.415-1.414l2.392 2.392 6.393-6.392a1 1 0 011.415 0z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </span>

                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $user->locale === 'en' ? $color['en'] : $color['label'] }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- Live preview --}}
                <div class="mt-6 rounded-2xl border border-gray-200 p-4 dark:border-gray-700">
                    <p class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                        {{ $user->locale === 'en' ? 'Live preview' : 'معاينة مباشرة' }}
                    </p>

                    <div class="rounded-xl border border-gray-200 p-4 transition-colors duration-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">
                                    OrderFlow
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->locale === 'en'
                                        ? 'Your business at a glance'
                                        : 'أعمالك ومبيعاتك في مكان واحد' }}
                                </p>
                            </div>

                            <span
                                class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors duration-200"
                                :style="'background-color: ' + colors[selectedColor]">
                                {{ $user->locale === 'en' ? 'New order' : 'طلب جديد' }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user->locale === 'en' ? 'Sales' : 'المبيعات' }}
                                </p>
                                <p class="mt-2 text-xl font-bold"
                                   :style="'color: ' + colors[selectedColor]">
                                    1,250
                                </p>
                            </div>

                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user->locale === 'en' ? 'Orders' : 'الطلبات' }}
                                </p>
                                <p class="mt-2 text-xl font-bold"
                                   :style="'color: ' + colors[selectedColor]">
                                    24
                                </p>
                            </div>

                            <div class="col-span-2 rounded-xl bg-gray-50 p-3 dark:bg-gray-800 sm:col-span-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user->locale === 'en' ? 'Status' : 'حالة النظام' }}
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full"
                                          :style="'background-color: ' + colors[selectedColor]"></span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $user->locale === 'en' ? 'Active' : 'نشط' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex justify-start">
                <button type="submit"
                        class="rounded-xl px-6 py-3 font-semibold text-white transition hover:brightness-90 focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="background-color: var(--accent); --tw-ring-color: var(--accent);">
                    {{ $user->locale === 'en' ? 'Save settings' : 'حفظ الإعدادات' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
