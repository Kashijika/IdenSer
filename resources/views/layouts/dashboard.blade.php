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
            justify-content: space-between;
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
        
        
        /* Role-based visibility */
        .admin-only { display: none; }
        .hr-only { display: none; }
        .employee-only { display: none; }
        
        .user-admin .admin-only { display: block; }
        .user-hr .hr-only { display: block; }
        .user-employee .employee-only { display: block; }
        
        .user-admin .hr-only { display: block; } /* Admins can see HR features */
        
        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
        
        .spinner {
            width: 2rem;
            height: 2rem;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #1e3a8a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Content transitions */
        #content-area {
            transition: opacity 0.3s ease;
        }
        
        #content-area.loading {
            opacity: 0.6;
        }
        
        .main-content {
            position: relative;
        }
        
        /* Alert styles */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.3s ease;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

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
<body class="dashboard-layout user-{{ isset($user['role_name']) ? $user['role_name'] : 'guest' }}">
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
                    <span>
                        @if(session('wso2_id_token_payload.given_name') && session('wso2_id_token_payload.family_name'))
                            {{ session('wso2_id_token_payload.given_name') }} {{ session('wso2_id_token_payload.family_name') }}
                        @elseif(isset($user['given_name']) && isset($user['family_name']))
                            {{ $user['given_name'] }} {{ $user['family_name'] }}
                        @else
                            {{ session('wso2_id_token_payload.email') ?? $user['email'] ?? 'Guest' }}
                        @endif
                    </span>
                    <span class="user-avatar">
                        @if(isset($user['initials']))
                            {{ $user['initials'] }}
                        @elseif(session('wso2_id_token_payload.given_name') && session('wso2_id_token_payload.family_name'))
                            {{ strtoupper(substr(session('wso2_id_token_payload.given_name'), 0, 1) . substr(session('wso2_id_token_payload.family_name'), 0, 1)) }}
                        @else
                            ?
                        @endif
                    </span>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;" id="logoutForm">
                    @csrf
                    <button type="button" class="logout-btn" title="Logout" onclick="performLogout()">
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
                        <a href="{{ route('dashboard') }}" class="nav-link" data-route="dashboard" data-url="{{ route('dashboard') }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item hr-only admin-only">
                        <a href="{{ route('dashboard.users') }}" class="nav-link" data-route="dashboard.users" data-url="{{ route('dashboard.users') }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li class="nav-item admin-only">
                        <a href="{{ route('dashboard.roles') }}" class="nav-link" data-route="dashboard.roles" data-url="{{ route('dashboard.roles') }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Roles & Permissions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('dashboard.trading-data') }}" class="nav-link" data-route="dashboard.trading-data" data-url="{{ route('dashboard.trading-data') }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span>Trading Data</span>
                        </a>
                    </li>
                    
                    <li class="nav-item admin-only">
                        <a href="{{ route('dashboard.security-policies') }}" class="nav-link" data-route="dashboard.security-policies" data-url="{{ route('dashboard.security-policies') }}">
                            <svg class="nav-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                            </svg>
                            <span>Security Policies</span>
                        </a>
                    </li>
                    
                    <li class="nav-item hr-only admin-only">
                        <a href="{{ route('dashboard.audit-logs') }}" class="nav-link" data-route="dashboard.audit-logs" data-url="{{ route('dashboard.audit-logs') }}">
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
        <main class="main-content" id="main-content">
            <!-- Loading overlay -->
            <div id="loading-overlay" class="loading-overlay" style="display: none;">
                <div class="spinner"></div>
            </div>

            <!-- Content area -->
            <div id="content-area">
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
            </div>
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
            // Initialize dashboard navigation
            initializeNavigation();
            
            // Set initial active state based on current URL
            setActiveNavigation();
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
        
        // Initialize AJAX navigation
        function initializeNavigation() {
            const navLinks = document.querySelectorAll('.nav-link[data-route]');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const url = this.getAttribute('data-url');
                    const route = this.getAttribute('data-route');
                    
                    if (url && route) {
                        navigateToPage(url, route, this);
                    }
                });
            });
        }
        
        // Navigate to a dashboard page
        function navigateToPage(url, route, clickedLink) {
            // Show loading state
            showPageLoading(true);
            
            // Update active navigation immediately for better UX
            updateActiveNavigation(clickedLink);
            
            // Make AJAX request for content
            fetch(url + '?ajax=1', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                // Update the content area
                updatePageContent(html);
                
                // Update browser history
                history.pushState({ route: route, url: url }, '', url);
                
                // Update page title
                updatePageTitle(route);
            })
            .catch(error => {
                console.error('Navigation error:', error);
                // Fallback to normal page load
                window.location.href = url;
            })
            .finally(() => {
                showPageLoading(false);
            });
        }
        
        // Update page content
        function updatePageContent(html) {
            const contentArea = document.getElementById('content-area');
            
            // Parse the response to extract only the content
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Look for the main content or use the entire response
            const mainContent = tempDiv.querySelector('.main-content');
            const newContent = mainContent ? mainContent.innerHTML : html;
            
            // Smooth transition
            contentArea.style.opacity = '0';
            setTimeout(() => {
                contentArea.innerHTML = newContent;
                contentArea.style.opacity = '1';
                
                // Re-initialize any JavaScript for the new content
                initializePageScripts();
            }, 150);
        }
        
        // Update active navigation state
        function updateActiveNavigation(activeLink) {
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked link
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }
        
        // Set initial active navigation based on current URL
        function setActiveNavigation() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link[data-url]');
            
            navLinks.forEach(link => {
                const linkUrl = new URL(link.getAttribute('data-url'), window.location.origin);
                if (linkUrl.pathname === currentPath) {
                    link.classList.add('active');
                }
            });
        }
        
        // Show/hide page loading
        function showPageLoading(show) {
            const loadingOverlay = document.getElementById('loading-overlay');
            const contentArea = document.getElementById('content-area');
            
            if (show) {
                loadingOverlay.style.display = 'flex';
                contentArea.classList.add('loading');
            } else {
                loadingOverlay.style.display = 'none';
                contentArea.classList.remove('loading');
            }
        }
        
        // Update page title based on route
        function updatePageTitle(route) {
            const titleMap = {
                'dashboard': 'Dashboard',
                'dashboard.users': 'Users Management',
                'dashboard.roles': 'Roles & Permissions',
                'dashboard.trading-data': 'Trading Data',
                'dashboard.security-policies': 'Security Policies',
                'dashboard.audit-logs': 'Audit Logs'
            };
            
            const title = titleMap[route] || 'Dashboard';
            document.title = `${title} - SWA Media Portal`;
        }
        
        // Initialize page-specific scripts
        function initializePageScripts() {
            // Re-run any initialization scripts for the new content
            const scripts = document.querySelectorAll('#content-area script');
            scripts.forEach(script => {
                if (script.src) {
                    // External script - reload it
                    const newScript = document.createElement('script');
                    newScript.src = script.src;
                    document.head.appendChild(newScript);
                } else {
                    // Inline script - execute it
                    try {
                        eval(script.textContent);
                    } catch (e) {
                        console.error('Script execution error:', e);
                    }
                }
            });
            
            // Re-initialize common functionality
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        }
        
        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.url) {
                // Navigate to the URL in history
                const navLink = document.querySelector(`[data-url="${e.state.url}"]`);
                if (navLink) {
                    navigateToPage(e.state.url, e.state.route, navLink);
                } else {
                    // Fallback to page reload
                    window.location.reload();
                }
            }
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

        // Aggressive logout function
        async function performLogout() {
            try {
                // Clear all localStorage and sessionStorage
                if (typeof(Storage) !== "undefined") {
                    localStorage.clear();
                    sessionStorage.clear();
                }

                // Clear all cookies for this domain
                const cookies = document.cookie.split(";");
                for (let cookie of cookies) {
                    const eqPos = cookie.indexOf("=");
                    const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
                    
                    // Delete for current path
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                    // Delete for root path
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;";
                    // Delete for current domain
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=" + window.location.hostname;
                    // Delete for parent domain
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=." + window.location.hostname;
                }

                // Submit the logout form
                document.getElementById('logoutForm').submit();
                
            } catch (error) {
                console.error('Logout error:', error);
                // Fallback: just submit the form
                document.getElementById('logoutForm').submit();
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>