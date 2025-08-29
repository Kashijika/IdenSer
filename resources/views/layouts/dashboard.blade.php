<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - SWA Media Portal</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('styles/globals.css') }}">
    
    @stack('styles')
    
    <style>
        .dashboard-layout {
            min-height: 100vh;
            background-color: #f9fafb;
        }
        
        .dashboard-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: between;
            max-width: 100%;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-section img {
            height: 2rem;
        }
        
        .brand-text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .brand-primary {
            font-size: 1.25rem;
            font-weight: 600;
            color: #ff6b35;
        }
        
        .brand-divider {
            color: #9ca3af;
        }
        
        .brand-secondary {
            color: #374151;
            font-weight: 500;
        }
        
        .brand-badge {
            background-color: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .user-avatar {
            width: 2rem;
            height: 2rem;
            background-color: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #374151;
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        .logout-btn {
            background: none;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .logout-btn:hover {
            background-color: #f9fafb;
            color: #374151;
        }
        
        .dashboard-container {
            display: flex;
            max-width: 100%;
        }
        
        .sidebar {
            width: 16rem;
            background: white;
            border-right: 1px solid #e5e7eb;
            min-height: calc(100vh - 73px);
            position: sticky;
            top: 73px;
        }
        
        .sidebar-nav {
            padding: 1rem;
        }
        
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            color: #374151;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
        }
        
        .nav-link:hover {
            background-color: #f9fafb;
            color: #111827;
        }
        
        .nav-link.active {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: 500;
        }
        
        .nav-icon {
            width: 1rem;
            height: 1rem;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: calc(100vw - 16rem);
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        
        .page-description {
            color: #6b7280;
        }
        
        .dashboard-footer {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
        }
        
        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 100%;
        }
        
        .footer-text {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .language-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .language-icon {
            width: 1rem;
            height: 1rem;
        }
        
        /* Role-based visibility */
        .admin-only { display: none; }
        .hr-only { display: none; }
        .employee-only { display: none; }
        
        .user-admin .admin-only { display: block; }
        .user-hr .hr-only { display: block; }
        .user-employee .employee-only { display: block; }
        
        .user-admin .hr-only { display: block; } /* Admins can see HR features */
        
        /* Responsive design */
        @media (max-width: 1024px) {
            .sidebar {
                width: 14rem;
            }
            
            .main-content {
                max-width: calc(100vw - 14rem);
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                min-height: auto;
                position: static;
            }
            
            .main-content {
                max-width: 100%;
                padding: 1rem;
            }
            
            .brand-text {
                font-size: 0.875rem;
            }
            
            .brand-badge {
                display: none;
            }
        }
    </style>
</head>
<body class="dashboard-layout user-{{ auth()->user()->role->name ?? 'guest' }}">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ asset('images/swa-logo.png') }}" alt="SWA Media" />
                <div class="brand-text">
                    <span class="brand-primary">SWA MEDIA</span>
                    <span class="brand-divider">|</span>
                    <span class="brand-secondary">IDENTITY SERVER</span>
                    <span class="brand-badge">Dashboard</span>
                </div>
            </div>
            
            <div class="user-section">
                <div class="user-info">
                    <span>{{ auth()->user()->full_name }}</span>
                    <span class="user-avatar">{{ auth()->user()->initials }}</span>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item hr-only admin-only">
                        <a href="{{ route('dashboard.users') }}" class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li class="nav-item admin-only">
                        <a href="{{ route('dashboard.roles') }}" class="nav-link {{ request()->routeIs('dashboard.roles') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Roles & Permissions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('dashboard.trading-data') }}" class="nav-link {{ request()->routeIs('dashboard.trading-data') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span>Trading Data</span>
                        </a>
                    </li>
                    
                    <li class="nav-item admin-only">
                        <a href="{{ route('dashboard.security-policies') }}" class="nav-link {{ request()->routeIs('dashboard.security-policies') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                            </svg>
                            <span>Security Policies</span>
                        </a>
                    </li>
                    
                    <li class="nav-item hr-only admin-only">
                        <a href="{{ route('dashboard.audit-logs') }}" class="nav-link {{ request()->routeIs('dashboard.audit-logs') ? 'active' : '' }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8.5 8a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0zm-3 3a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                            <span>Audit Logs</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('account') }}" class="nav-link">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 8a2 2 0 110-4 2 2 0 010 4zM10 18l3-3h-6l3 3z"/>
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                            </svg>
                            <span>My Account</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-content">
            <div class="footer-text">SWA Media Identity Server Dashboard</div>
            <div class="language-selector">
                <svg class="language-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.188-.196-.373-.396-.554-.6a19.098 19.098 0 01-3.107 3.567 1 1 0 01-1.334-1.49 17.087 17.087 0 003.13-3.733 18.992 18.992 0 01-1.487-2.494 1 1 0 111.79-.89c.234.47.489.928.764 1.372.417-.934.752-1.913.997-2.927H3a1 1 0 110-2h3V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 5.982a.869.869 0 01.02.037l.99 1.98a1 1 0 11-1.79.895L15.383 16h-4.764l-.724 1.447a1 1 0 11-1.788-.894l.99-1.98.019-.038 2.99-5.982A1 1 0 0113 8zm-1.382 6h2.764L13 12.236 11.618 14z"/>
                </svg>
                <span>English (United States)</span>
                <svg class="language-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                </svg>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Common dashboard functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
        
        // Utility functions for AJAX requests
        function makeRequest(url, options = {}) {
            const defaults = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            };
            
            return fetch(url, { ...defaults, ...options })
                .then(response => response.json());
        }
        
        function showLoading(element, isLoading) {
            if (isLoading) {
                element.disabled = true;
                element.style.opacity = '0.6';
            } else {
                element.disabled = false;
                element.style.opacity = '1';
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>