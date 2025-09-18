<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'My Account') - SWA Media Portal</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('styles/globals.css') }}">
    
    @stack('styles')

    <style>
        .account-layout {
            min-height: 100vh;
            background-color: #f9fafb;
            display: flex;
            flex-direction: column;
        }
        
        .account-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
        }
        
        .account-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
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
            font-weight: 600;
        }
        
        .brand-name {
            color: #1e3a8a;
        }
        
        .brand-separator {
            color: #6b7280;
        }
        
        .brand-identity {
            color: #374151;
        }
        
        .account-badge {
            background-color: #1e3a8a;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
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
            gap: 0.75rem;
        }
        
        .user-name {
            font-weight: 500;
            color: #374151;
        }
        
        .user-avatar {
            width: 2rem;
            height: 2rem;
            background-color: #1e3a8a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .logout-btn {
            background: none;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem;
            color: #6b7280;
            cursor: pointer;
            transition: colors 0.2s;
        }
        
        .logout-btn:hover {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .account-main {
            flex: 1;
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            gap: 2rem;
            padding: 2rem 1.5rem;
        }
        
        .account-sidebar {
            width: 250px;
            flex-shrink: 0;
        }
        
        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .sidebar-nav a:hover {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .sidebar-nav a.active {
            background-color: #1e3a8a;
            color: white;
        }
        
        .sidebar-nav svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        
        .account-content {
            flex: 1;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 2rem;
        }
        
        .account-footer {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
        }
        
        .account-footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-text {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .footer-language {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .footer-language svg {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>

<body>
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
                        <span class="user-name">
                            @if(isset($user['given_name']))
                                {{ $user['given_name'] }}
                            @elseif(isset($user['first_name']))
                                {{ $user['first_name'] }}
                            @elseif(isset($user['name']))
                                {{ $user['name'] }}
                            @else
                                {{ $user['email'] ?? 'User' }}
                            @endif
                        </span>
                        <div class="user-avatar">
                            @if(isset($user['initials']))
                                {{ $user['initials'] }}
                            @else
                                {{ strtoupper(substr($user['email'] ?? 'U', 0, 1)) }}
                            @endif
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
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                Overview
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.personal-info') }}" class="{{ request()->routeIs('account.personal-info') ? 'active' : '' }}">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Personal Info
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.security') }}" class="{{ request()->routeIs('account.security') ? 'active' : '' }}">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Security
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Content Area -->
            <main class="account-content">
                @yield('content')
            </main>
        </div>

        <!-- Footer -->
        <footer class="account-footer">
            <div class="account-footer-content">
                <div class="footer-left">
                    <span class="footer-text">SWA Media Identity Server Dashboard</span>
                </div>
                <div class="footer-right">
                    <div class="footer-language">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                        </svg>
                        <span>English (United States)</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    @stack('scripts')
    
    <script>
    // Global AJAX request helper
    async function makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };

        const response = await fetch(url, { ...defaultOptions, ...options });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }
        
        return await response.text();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('logoutBtn');
        
        if (logoutBtn) {
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
        }
    });
    </script>
</body>
</html>
