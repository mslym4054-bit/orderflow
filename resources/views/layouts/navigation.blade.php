
<nav x-data="{ open: false }" class="bg-surface border-b border-line">

    {{-- Primary Navigation Menu --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            <div class="flex items-center">

                {{-- Logo --}}
                <div class="shrink-0 flex items-center gap-2">

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">

                        <svg
                            class="w-8 h-8 text-neon"
                            viewBox="0 0 32 32"
                            fill="none"
                        >
                            <path
                                d="M4 22c4 0 4-6 8-6s4 6 8 6 4-6 8-6"
                                stroke="currentColor"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            <path
                                d="M22 10l6 6-6 6"
                                stroke="currentColor"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <span class="text-ink font-bold text-lg hidden sm:inline">
                            OrderFlow
                        </span>

                    </a>

                </div>


                {{-- Navigation Links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">


                    {{-- لوحة التحكم --}}
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 text-sm font-medium
                        {{ request()->routeIs('dashboard')
                            ? 'border-neon text-ink'
                            : 'border-transparent text-muted hover:text-ink hover:border-line' }}"
                    >

                        <x-icon name="grid" />

                        لوحة التحكم

                    </a>


                    {{-- الطلبات --}}
                    <a
                        href="{{ route('orders.index') }}"
                        class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 text-sm font-medium
                        {{ request()->routeIs('orders.*')
                            ? 'border-neon text-ink'
                            : 'border-transparent text-muted hover:text-ink hover:border-line' }}"
                    >

                       <x-icon name="clipboard" />

                        الطلبات

                    </a>


                    {{-- العملاء --}}
                    <a
                        href="{{ route('customers.index') }}"
                        class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 text-sm font-medium
                        {{ request()->routeIs('customers.*')
                            ? 'border-neon text-ink'
                            : 'border-transparent text-muted hover:text-ink hover:border-line' }}"
                    >

                        <x-icon name="users" />

                        العملاء

                    </a>


                    {{-- المنتجات --}}
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 text-sm font-medium
                        {{ request()->routeIs('products.*')
                            ? 'border-neon text-ink'
                            : 'border-transparent text-muted hover:text-ink hover:border-line' }}"
                    >

                        <x-icon name="clipboard" />

                        المنتجات

                    </a>


                    {{-- التقارير --}}
                    <a
                        href="{{ route('reports') }}"
                        class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 text-sm font-medium
                        {{ request()->routeIs('reports')
                            ? 'border-neon text-ink'
                            : 'border-transparent text-muted hover:text-ink hover:border-line' }}"
                    >

                        <x-icon name="chart" />

                        التقارير

                    </a>

                </div>

            </div>


            {{-- Settings Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">

                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 border border-line text-sm leading-4 font-medium rounded-md text-muted bg-surface2 hover:text-ink hover:border-neon/40 focus:outline-none transition ease-in-out duration-150"
                        >

                            <x-icon name="user" />

                            <div>
                                {{ Auth::user()->name }}
                            </div>

                            <x-icon
                                name="chevron-down"
                                class="w-3 h-3"
                            />

                        </button>

                    </x-slot>


                    <x-slot name="content">

                        {{-- الملف الشخصي --}}
                        <x-dropdown-link :href="route('profile.edit')">

                            <span class="flex items-center gap-2">

                                <x-icon
                                    name="gear"
                                    class="w-4 h-4"
                                />

                                الملف الشخصي

                            </span>

                        </x-dropdown-link>


                        {{-- تسجيل الخروج --}}
                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >

                                <span class="flex items-center gap-2">

                                    <x-icon
                                        name="logout"
                                        class="w-4 h-4"
                                    />

                                    تسجيل الخروج

                                </span>

                            </x-dropdown-link>

                        </form>

                    </x-slot>

                </x-dropdown>

            </div>


            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">

                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-muted hover:text-ink hover:bg-surface2 focus:outline-none focus:bg-surface2 focus:text-ink transition duration-150 ease-in-out"
                >

                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >

                        <path
                            :class="{
                                'hidden': open,
                                'inline-flex': ! open
                            }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{
                                'hidden': ! open,
                                'inline-flex': open
                            }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />

                    </svg>

                </button>

            </div>

        </div>

    </div>


    {{-- Responsive Navigation Menu --}}
    <div
        :class="{
            'block': open,
            'hidden': ! open
        }"
        class="hidden sm:hidden bg-surface"
    >

        <div class="pt-2 pb-3 space-y-1">


            {{-- لوحة التحكم --}}
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-2 ps-3 pe-4 py-2 border-s-4 text-base font-medium
                {{ request()->routeIs('dashboard')
                    ? 'border-neon text-ink bg-surface2'
                    : 'border-transparent text-muted' }}"
            >

                <x-icon name="grid" />

                لوحة التحكم

            </a>


            {{-- الطلبات --}}
            <a
                href="{{ route('orders.index') }}"
                class="flex items-center gap-2 ps-3 pe-4 py-2 border-s-4 text-base font-medium
                {{ request()->routeIs('orders.*')
                    ? 'border-neon text-ink bg-surface2'
                    : 'border-transparent text-muted' }}"
            >

                <x-icon name="clipboard" />

                الطلبات

            </a>


            {{-- العملاء --}}
            <a
                href="{{ route('customers.index') }}"
                class="flex items-center gap-2 ps-3 pe-4 py-2 border-s-4 text-base font-medium
                {{ request()->routeIs('customers.*')
                    ? 'border-neon text-ink bg-surface2'
                    : 'border-transparent text-muted' }}"
            >

                <x-icon name="users" />

                العملاء

            </a>


            {{-- المنتجات --}}
            <a
                href="{{ route('products.index') }}"
                class="flex items-center gap-2 ps-3 pe-4 py-2 border-s-4 text-base font-medium
                {{ request()->routeIs('products.*')
                    ? 'border-neon text-ink bg-surface2'
                    : 'border-transparent text-muted' }}"
            >

              <x-icon name="clipboard" />

                المنتجات

            </a>


            {{-- التقارير --}}
            <a
                href="{{ route('reports') }}"
                class="flex items-center gap-2 ps-3 pe-4 py-2 border-s-4 text-base font-medium
                {{ request()->routeIs('reports')
                    ? 'border-neon text-ink bg-surface2'
                    : 'border-transparent text-muted' }}"
            >

                <x-icon name="chart" />

                التقارير

            </a>

        </div>


        {{-- Responsive Settings Options --}}
        <div class="pt-4 pb-1 border-t border-line">

            <div class="px-4">

                <div class="font-medium text-base text-ink">
                    {{ Auth::user()->name }}
                </div>

                <div class="font-medium text-sm text-muted">
                    {{ Auth::user()->email }}
                </div>

            </div>


            <div class="mt-3 space-y-1">


                {{-- الملف الشخصي --}}
                <x-responsive-nav-link :href="route('profile.edit')">

                    <span class="flex items-center gap-2">

                        <x-icon
                            name="gear"
                            class="w-4 h-4"
                        />

                        الملف الشخصي

                    </span>

                </x-responsive-nav-link>


                {{-- تسجيل الخروج --}}
                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <x-responsive-nav-link
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >

                        <span class="flex items-center gap-2">

                            <x-icon
                                name="logout"
                                class="w-4 h-4"
                            />

                            تسجيل الخروج

                        </span>

                    </x-responsive-nav-link>

                </form>

            </div>

        </div>

    </div>

</nav>
