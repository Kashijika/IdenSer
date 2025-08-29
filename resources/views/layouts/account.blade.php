@extends('layouts.app')

@push('styles')
<style>
    .account-layout {
        min-height: 100vh;
        background-color: #f9fafb;
    }
    
    .account-header {
        background-color: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 1.5rem;
    }
    
    .account-header-content {
        max-width: 112rem;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
    }
    
    .account-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .account-header-left img {
        height: 2rem;
    }
    
    .account-header-brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .brand-name {
        font-size: 1.25rem;
        font-weight: 500;
        color: var(--swa-orange);
    }
    
    .brand-separator {
        color: #9ca3af;
    }
    
    .brand-identity {
        color: #374151;
        font-weight: 500;
    }
    
    .account-badge {
        background-color: #f3f4f6;
        color: #374151;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .account-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .user-name {
        font-size: 0.875rem;
        color: #374151;
    }
    
    .user-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background-color: #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .logout-btn {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    
    .logout-btn:hover {
        background-color: #f3f4f6;
    }
    
    .account-main {
        display: flex;
        max-width: 112rem;
        margin: 0 auto;
    }
    
    .account-sidebar {
        width: 16rem;
        background-color: white;
        border-right: 1px solid #e5e7eb;
        min-height: calc(100vh - 73px);
    }
    
    .sidebar-nav {
        padding: 1rem;
    }
    
    .sidebar-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-nav li {
        margin-bottom: 0.25rem;
    }
    
    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .sidebar-nav a:hover {
        background-color: #f9fafb;
    }
    
    .sidebar-nav a.active {
        background-color: #f3f4f6;
        color: #111827;
        font-weight: 500;
    }
    
    .sidebar-nav svg {
        width: 1rem;
        height: 1rem;
    }
    
    .account-content {
        flex: 1;
        padding: 2rem;
    }
    
    .account-footer {
        background-color: white;
        border-top: 1px solid #e5e7eb;
        padding: 0 1.5rem;
    }
    
    .account-footer-content {
        max-width: 112rem;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
    }
    
    .footer-brand {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .footer-language {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
    }
    
    .footer-language svg {
        width: 1rem;
        height: 1rem;
    }
    
    .footer-language span {
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="account-layout">
    <!-- Header -->
    <header class="account-header">
        <div class="account-header-content">
            <div class="account-header-left">
                <img src="{{ asset('images/swa-logo.png') }}" alt="SWA Media" />
                <div class="account-header-brand">
                    <span class="brand-name">SWA MEDIA</span>
                    <span class="brand-separator">|</span>
                    <span class="brand-identity">IDENTITY SERVER</span>
                    <span class="account-badge">My Account</span>
                </div>
            </div>
            
            <div class="account-header-right">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->first_name ?? Auth::user()->name }}</span>
                    <div class="user-avatar">
                        {{ Auth::user()->initials }}
                    </div>
                </div>
                <button type="button" class="logout-btn" id="logoutBtn">
                    <svg fill="currentColor" viewBox="0 0 20 20" width="16" height="16">
                        <path d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="account-main">
        <!-- Sidebar -->
        <div class="account-sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="{{ route('account.overview') }}" class="{{ request()->routeIs('account.overview') ? 'active' : '' }}">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            <span>Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.personal-info') }}" class="{{ request()->routeIs('account.personal-info') ? 'active' : '' }}">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                            <span>Personal Info</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.security') }}" class="{{ request()->routeIs('account.security') ? 'active' : '' }}">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span>Security</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="account-content">
            @yield('account-content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="account-footer">
        <div class="account-footer-content">
            <div class="footer-brand">SWA Media Identity Server</div>
            <div class="footer-language">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z"/>
                </svg>
                <span>English (United States)</span>
                <svg fill="currentColor" viewBox="0 0 20 20" width="12" height="12">
                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                </svg>
            </div>
        </div>
    </footer>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    logoutBtn.addEventListener('click', async function() {
        if (confirm('Are you sure you want to log out?')) {
            try {
                const response = await makeRequest('{{ route("auth.logout") }}', {
                    method: 'POST'
                });
                
                if (response.success) {
                    window.location.href = response.redirect_url || '{{ route("login") }}';
                } else {
                    alert('Logout failed. Please try again.');
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Fallback: redirect to login page
                window.location.href = '{{ route("login") }}';
            }
        }
    });
});
</script>
@endpush