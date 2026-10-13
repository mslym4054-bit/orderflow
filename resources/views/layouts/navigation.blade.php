
@php
    $isEnglish = (auth()->user()?->locale ?? 'ar') === 'en';
@endphp

<nav x-data="{ open: false }" class="of-navigation">

    {{-- Brand --}}
    <div class="of-brand">
        <a href="{{ route('dashboard') }}" class="of-brand-link">
            <svg class="of-brand-logo" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <path d="M4 22c4 0 4-6 8-6s4 6 8 6 4-6 8-6"
                    stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 10l6 6-6 6"
                    stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>OrderFlow</span>
        </a>

        <span class="of-brand-caption">
            {{ $isEnglish ? 'Manage your store smarter' : 'إدارة متجرك بذكاء' }}
        </span>
    </div>

    {{-- Main navigation --}}
    <div class="of-nav-section">
        <div class="of-nav-heading">
            {{ $isEnglish ? 'MAIN MENU' : 'القائمة الرئيسية' }}
        </div>

        <a href="{{ route('dashboard') }}"
           class="of-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <x-icon name="grid" />
            <span>{{ $isEnglish ? 'Dashboard' : 'لوحة التحكم' }}</span>
        </a>

        <a href="{{ route('orders.index') }}"
           class="of-nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
            <x-icon name="orders" />
            <span>{{ $isEnglish ? 'Orders' : 'الطلبات' }}</span>
        </a>

        <a href="{{ route('customers.index') }}"
           class="of-nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <x-icon name="users" />
            <span>{{ $isEnglish ? 'Customers' : 'العملاء' }}</span>
        </a>

        <a href="{{ route('products.index') }}"
           class="of-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <x-icon name="box" />
            <span>{{ $isEnglish ? 'Products' : 'المنتجات' }}</span>
        </a>

        <a href="{{ route('reports') }}"
           class="of-nav-link {{ request()->routeIs('reports') ? 'active' : '' }}">
            <x-icon name="chart" />
            <span>{{ $isEnglish ? 'Reports' : 'التقارير' }}</span>
        </a>
    </div>

    <div class="of-nav-spacer"></div>

    {{-- Account --}}
    <div class="of-account">
        <div class="of-nav-heading">
            {{ $isEnglish ? 'ACCOUNT' : 'الحساب' }}
        </div>

        <div class="of-account-user">
            <div class="of-avatar">
                {{ mb_substr(Auth::user()->name, 0, 1) }}
            </div>

            <div class="of-account-details">
                <div class="of-account-name">{{ Auth::user()->name }}</div>
                <div class="of-account-email">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="of-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <x-icon name="user" />
            <span>{{ $isEnglish ? 'Profile' : 'الملف الشخصي' }}</span>
        </a>

        <a href="{{ route('settings.edit') }}"
           class="of-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <x-icon name="gear" />
            <span>{{ $isEnglish ? 'Settings' : 'الإعدادات' }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="of-nav-link of-logout">
                <x-icon name="logout" />
                <span>{{ $isEnglish ? 'Log out' : 'تسجيل الخروج' }}</span>
            </button>
        </form>
    </div>

    {{-- Mobile navigation toggle --}}
    <button type="button"
            class="of-mobile-nav-toggle"
            @click="open = !open"
            :aria-expanded="open.toString()">
        <span x-show="!open">
            {{ $isEnglish ? 'Show account options' : 'عرض خيارات الحساب' }}
        </span>
        <span x-show="open" x-cloak>
            {{ $isEnglish ? 'Hide account options' : 'إخفاء خيارات الحساب' }}
        </span>
    </button>

</nav>

<style>
    .of-navigation {
        --nav-hover-bg: var(--sidebar-hover);
        --nav-active-bg: var(--sidebar-active);
        --nav-active-text: var(--sidebar-active-text);
        --nav-active-border: var(--sidebar-active-border);
        --nav-divider: var(--sidebar-border);
        --nav-brand-text: var(--sidebar-text);
        --nav-logo: var(--accent);
        --nav-avatar-bg: var(--accent-soft);
        --nav-avatar-text: var(--accent);
        --nav-logout-hover-bg: #fef2f2;
        --nav-logout-hover-text: #b91c1c;

        min-height: 100%;
        display: flex;
        flex-direction: column;
        padding: 24px 16px;
        background: var(--sidebar);
        color: var(--sidebar-text);
    }

    /* Dark mode colors */
    html[data-theme="dark"] .of-navigation {
        --nav-hover-bg: #172820;
        --nav-active-bg: #173c2d;
        --nav-active-text: #53e5ad;
        --nav-active-border: #286b4e;
        --nav-divider: #2a3831;
        --nav-brand-text: #f0fff7;
        --nav-logo: #39d9a0;
        --nav-avatar-bg: rgba(57, 217, 160, .15);
        --nav-avatar-text: #53e5ad;
        --nav-logout-hover-bg: #3a2020;
        --nav-logout-hover-text: #fca5a5;
    }

    .of-brand {
        padding: 4px 8px 28px;
        border-bottom: 1px solid var(--nav-divider);
        margin-bottom: 24px;
    }

    .of-navigation .of-brand-link {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--nav-brand-text);
        text-decoration: none;
        font-size: 23px;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .of-navigation .of-brand-logo {
        width: 36px;
        height: 36px;
        color: var(--nav-logo);
        flex-shrink: 0;
    }

    .of-navigation .of-brand-caption {
        display: block;
        margin: 7px 0 0;
        margin-inline-start: 46px;
        color: var(--sidebar-muted);
        font-size: 12px;
    }

    .of-nav-section {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .of-navigation .of-nav-heading {
        padding: 0 12px 10px;
        color: var(--sidebar-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
    }

    .of-navigation .of-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid transparent;
        border-radius: 10px;
        background: transparent;
        color: var(--sidebar-text);
        text-align: start;
        text-decoration: none;
        font-family: inherit;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition:
            background-color .18s ease,
            color .18s ease,
            border-color .18s ease;
    }

    .of-navigation .of-nav-link svg {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
    }

    /* Hover */
    .of-navigation .of-nav-link:hover {
        background: var(--nav-hover-bg) !important;
        color: var(--nav-active-text) !important;
        border-color: var(--nav-active-border);
    }

    /* Active page */
    .of-navigation .of-nav-link.active {
        background: var(--nav-active-bg) !important;
        border-color: var(--nav-active-border) !important;
        color: var(--nav-active-text) !important;
        font-weight: 700;
    }

    .of-navigation .of-nav-link.active svg {
        color: var(--nav-active-text);
    }

    .of-navigation .of-nav-link:focus-visible,
    .of-navigation .of-mobile-nav-toggle:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    .of-nav-spacer {
        flex: 1;
        min-height: 35px;
    }

    .of-account {
        padding-top: 20px;
        border-top: 1px solid var(--nav-divider);
    }

    .of-account-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 8px 16px;
        min-width: 0;
    }

    .of-navigation .of-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        flex-shrink: 0;
        background: var(--nav-avatar-bg);
        color: var(--nav-avatar-text);
        font-weight: 800;
    }

    .of-account-details {
        min-width: 0;
    }

    .of-navigation .of-account-name {
        overflow: hidden;
        color: var(--sidebar-text);
        font-size: 13px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .of-navigation .of-account-email {
        overflow: hidden;
        margin-top: 4px;
        color: var(--sidebar-muted);
        font-size: 11px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .of-navigation .of-logout {
        font-family: inherit;
    }

    .of-navigation .of-logout:hover {
        background: var(--nav-logout-hover-bg) !important;
        color: var(--nav-logout-hover-text) !important;
        border-color: transparent;
    }

    .of-mobile-nav-toggle {
        display: none;
        margin-top: 12px;
        padding: 10px;
        border: 1px solid var(--nav-divider);
        border-radius: 9px;
        background: transparent;
        color: var(--sidebar-text);
        cursor: pointer;
    }

    [x-cloak] {
        display: none !important;
    }

    @media (max-width: 900px) {
        .of-navigation {
            min-height: 100%;
        }

        .of-mobile-nav-toggle {
            display: block;
        }
    }
</style>
