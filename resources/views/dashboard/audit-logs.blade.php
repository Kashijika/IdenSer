@extends('layouts.dashboard')

@section('title', 'Audit Logs')

@push('styles')
<style>
    .audit-container {
        max-width: none;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background: #1e3a8a;
        color: white;
    }
    
    .btn-primary:hover {
        background: #1e40af;
        color: white;
    }
    
    .btn-secondary {
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .btn-secondary:hover {
        background: #f9fafb;
        color: #374151;
    }
    
    .btn-success {
        background: #059669;
        color: white;
    }
    
    .btn-success:hover {
        background: #047857;
        color: white;
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .filters-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .filters-header {
        display: flex;
        align-items: center;
        justify-content: between;
        margin-bottom: 1.5rem;
    }
    
    .filters-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-label {
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }
    
    .form-input,
    .form-select {
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background: white;
        transition: all 0.2s ease;
    }
    
    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        align-items: center;
    }
    
    .export-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .export-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 100;
        min-width: 150px;
        margin-top: 0.25rem;
    }
    
    .export-menu.active {
        display: block;
    }
    
    .export-menu button {
        width: 100%;
        padding: 0.75rem 1rem;
        border: none;
        background: none;
        text-align: left;
        color: #374151;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .export-menu button:hover {
        background: #f9fafb;
    }
    
    .export-menu button:first-child {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .export-menu button:last-child {
        border-radius: 0 0 0.5rem 0.5rem;
    }
    
    .stats-bar {
        display: flex;
        gap: 2rem;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 1.5rem;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .stat-value {
        font-weight: 600;
        color: #111827;
    }
    
    .logs-table-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .table-header {
        background: #f9fafb;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .table-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
    }
    
    .auto-refresh {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .refresh-indicator {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: #10b981;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .table-container {
        overflow-x: auto;
        max-height: 600px;
        overflow-y: auto;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th,
    .table td {
        padding: 1rem 1.5rem;
        text-align: left;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .table th {
        background: #f9fafb;
        font-weight: 600;
        color: #374151;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .table tbody tr:hover {
        background: #f9fafb;
    }
    
    .log-user {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .user-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: #e5e7eb;
        color: #374151;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .user-info {
        min-width: 0;
    }
    
    .user-name {
        font-weight: 500;
        color: #111827;
        font-size: 0.875rem;
    }
    
    .user-role {
        color: #6b7280;
        font-size: 0.75rem;
    }
    
    .action-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .action-badge.login {
        background: #d1fae5;
        color: #065f46;
    }
    
    .action-badge.logout {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .action-badge.create {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .action-badge.update {
        background: #fef3c7;
        color: #92400e;
    }
    
    .action-badge.delete {
        background: #fecaca;
        color: #dc2626;
    }
    
    .action-badge.access {
        background: #e0e7ff;
        color: #3730a3;
    }
    
    .action-badge.security {
        background: #f3e8ff;
        color: #7c3aed;
    }
    
    .action-badge.system {
        background: #f3f4f6;
        color: #374151;
    }
    
    .action-icon {
        width: 0.875rem;
        height: 0.875rem;
    }
    
    .log-details {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .log-details .highlight {
        color: #111827;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-badge.success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-badge.warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-badge.error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .timestamp {
        color: #6b7280;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    .timestamp-main {
        font-weight: 500;
        color: #374151;
    }
    
    .timestamp-sub {
        font-size: 0.75rem;
        color: #9ca3af;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 2rem;
        padding: 1.5rem;
    }
    
    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        text-decoration: none;
        color: #374151;
        font-size: 0.875rem;
    }
    
    .pagination a:hover {
        background: #f9fafb;
    }
    
    .pagination .current {
        background: #1e3a8a;
        color: white;
        border-color: #1e3a8a;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
    }
    
    .empty-state-icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        opacity: 0.5;
    }
    
    .empty-state-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .empty-state-description {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }
    
    /* Loading state */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
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
        z-index: 20;
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
    
    /* Responsive design */
    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }
        
        .stats-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem 1rem;
        }
        
        .log-details {
            max-width: 200px;
        }
    }
</style>
@endpush

@section('content')
<div class="audit-container">
    <div class="page-header">
        <h1 class="page-title">Audit Logs</h1>
        <p class="page-description">Monitor system activities and security events</p>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <div class="filters-header">
            <h3 class="filters-title">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter Logs
            </h3>
        </div>

        <form id="filterForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label" for="dateFrom">Date From</label>
                    <input type="date" id="dateFrom" name="date_from" class="form-input" 
                           value="{{ request('date_from', now()->subDays(7)->format('Y-m-d')) }}">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="dateTo">Date To</label>
                    <input type="date" id="dateTo" name="date_to" class="form-input" 
                           value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="userFilter">User</label>
                    <select id="userFilter" name="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="actionFilter">Action Type</label>
                    <select id="actionFilter" name="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="user_login" {{ request('action') == 'user_login' ? 'selected' : '' }}>User Login</option>
                        <option value="user_logout" {{ request('action') == 'user_logout' ? 'selected' : '' }}>User Logout</option>
                        <option value="user_created" {{ request('action') == 'user_created' ? 'selected' : '' }}>User Created</option>
                        <option value="user_updated" {{ request('action') == 'user_updated' ? 'selected' : '' }}>User Updated</option>
                        <option value="user_deleted" {{ request('action') == 'user_deleted' ? 'selected' : '' }}>User Deleted</option>
                        <option value="role_changed" {{ request('action') == 'role_changed' ? 'selected' : '' }}>Role Changed</option>
                        <option value="password_changed" {{ request('action') == 'password_changed' ? 'selected' : '' }}>Password Changed</option>
                        <option value="security_policy_updated" {{ request('action') == 'security_policy_updated' ? 'selected' : '' }}>Security Policy Updated</option>
                        <option value="data_exported" {{ request('action') == 'data_exported' ? 'selected' : '' }}>Data Exported</option>
                        <option value="failed_login" {{ request('action') == 'failed_login' ? 'selected' : '' }}>Failed Login</option>
                        <option value="account_locked" {{ request('action') == 'account_locked' ? 'selected' : '' }}>Account Locked</option>
                        <option value="system_error" {{ request('action') == 'system_error' ? 'selected' : '' }}>System Error</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="statusFilter">Status</label>
                    <select id="statusFilter" name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="searchQuery">Search</label>
                    <input type="text" id="searchQuery" name="search" class="form-input" 
                           placeholder="Search logs..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="filter-actions">
                <div class="export-dropdown">
                    <button type="button" class="btn btn-success" id="exportBtn">
                        <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3.293 12.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 8.414V15a1 1 0 11-2 0V8.414L4.707 12.707a1 1 0 01-1.414 0z"/>
                        </svg>
                        Export Logs
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                    
                    <div class="export-menu" id="exportMenu">
                        <button type="button" data-format="csv">
                            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                            </svg>
                            Export as CSV
                        </button>
                        <button type="button" data-format="excel">
                            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
                            </svg>
                            Export as Excel
                        </button>
                        <button type="button" data-format="pdf">
                            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                            </svg>
                            Export as PDF
                        </button>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" id="clearFilters">
                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                    </svg>
                    Clear Filters
                </button>

                <button type="submit" class="btn btn-primary">
                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
                    </svg>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Bar -->
    <div class="stats-bar">
        <div class="stat-item">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
            </svg>
            <span>Total Logs: <span class="stat-value">{{ $auditLogs->total() }}</span></span>
        </div>
        
        <div class="stat-item">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
            </svg>
            <span>Success: <span class="stat-value">{{ $stats['success_count'] }}</span></span>
        </div>
        
        <div class="stat-item">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z"/>
            </svg>
            <span>Warnings: <span class="stat-value">{{ $stats['warning_count'] }}</span></span>
        </div>
        
        <div class="stat-item">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
            </svg>
            <span>Errors: <span class="stat-value">{{ $stats['error_count'] }}</span></span>
        </div>
        
        <div class="stat-item">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Unique Users: <span class="stat-value">{{ $stats['unique_users'] }}</span></span>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="logs-table-container" id="logsContainer">
        <div class="table-header">
            <h3 class="table-title">Audit Trail</h3>
            <div class="auto-refresh">
                <div class="refresh-indicator"></div>
                <span>Auto-refreshing every 30s</span>
                <button type="button" class="btn btn-sm btn-secondary" id="toggleRefresh">
                    <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"/>
                    </svg>
                    Pause
                </button>
            </div>
        </div>

        <div class="table-container">
            @if($auditLogs->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>Date/Time</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    @foreach($auditLogs as $log)
                    <tr>
                        <td>
                            <div class="log-user">
                                <div class="user-avatar">
                                    @if($log->user)
                                        {{ substr($log->user->first_name, 0, 1) }}{{ substr($log->user->last_name, 0, 1) }}
                                    @else
                                        <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="user-info">
                                    <div class="user-name">
                                        @if($log->user)
                                            {{ $log->user->first_name }} {{ $log->user->last_name }}
                                        @else
                                            System
                                        @endif
                                    </div>
                                    <div class="user-role">
                                        @if($log->user && $log->user->role)
                                            {{ $log->user->role->display_name }}
                                        @else
                                            Automated
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="action-badge {{ $log->action_type }}">
                                @switch($log->action)
                                    @case('user_login')
                                        <svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3z"/>
                                        </svg>
                                        Login
                                        @break
                                    @case('user_logout')
                                        <svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1z"/>
                                        </svg>
                                        Logout
                                        @break
                                    @case('user_created')
                                        <svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"/>
                                        </svg>
                                        User Created
                                        @break
                                    @case('user_updated')
                                        <svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793z"/>
                                        </svg>
                                        User Updated
                                        @break
                                    @case('user_deleted')
                                        <svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        </svg>
                                        User Deleted
                                        @break
                                    @default
                                        <svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16z"/>
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <div class="log-details">
                                {{ $log->description }}
                                @if($log->target_type && $log->target_id)
                                <div style="margin-top: 0.25rem; font-size: 0.75rem; color: #9ca3af;">
                                    Target: <span class="highlight">{{ class_basename($log->target_type) }} #{{ $log->target_id }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $log->status ?? 'success' }}">
                                @switch($log->status ?? 'success')
                                    @case('success')
                                        <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Success
                                        @break
                                    @case('warning')
                                        <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z"/>
                                        </svg>
                                        Warning
                                        @break
                                    @case('error')
                                        <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                        </svg>
                                        Error
                                        @break
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <div class="timestamp">
                                <code style="font-size: 0.75rem; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </code>
                            </div>
                        </td>
                        <td>
                            <div class="timestamp">
                                <div class="timestamp-main">{{ $log->created_at->format('M j, Y') }}</div>
                                <div class="timestamp-sub">{{ $log->created_at->format('H:i:s') }}</div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <svg class="empty-state-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
                </svg>
                <h4 class="empty-state-title">No Audit Logs Found</h4>
                <p class="empty-state-description">
                    No logs match your current filter criteria. Try adjusting your search parameters.
                </p>
                <button type="button" class="btn btn-primary" id="resetFilters">
                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1z"/>
                    </svg>
                    Reset Filters
                </button>
            </div>
            @endif
        </div>

        @if($auditLogs->hasPages())
        <div class="pagination">
            {{ $auditLogs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const exportBtn = document.getElementById('exportBtn');
    const exportMenu = document.getElementById('exportMenu');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const toggleRefreshBtn = document.getElementById('toggleRefresh');
    const logsContainer = document.getElementById('logsContainer');
    
    let refreshInterval;
    let isRefreshEnabled = true;
    
    // Export dropdown functionality
    exportBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        exportMenu.classList.toggle('active');
    });
    
    // Close export menu when clicking outside
    document.addEventListener('click', function() {
        exportMenu.classList.remove('active');
    });
    
    // Export functionality
    document.querySelectorAll('[data-format]').forEach(button => {
        button.addEventListener('click', function() {
            const format = this.getAttribute('data-format');
            exportLogs(format);
        });
    });
    
    function exportLogs(format) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        showLoading(exportBtn, true);
        
        fetch(`/dashboard/audit-logs/export/${format}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `audit-logs-${new Date().toISOString().split('T')[0]}.${format}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Export error:', error);
            alert(`Error exporting ${format.toUpperCase()}`);
        })
        .finally(() => {
            showLoading(exportBtn, false);
            exportMenu.classList.remove('active');
        });
    }
    
    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            filterForm.reset();
            
            // Set default date range (last 7 days)
            const today = new Date();
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            
            document.getElementById('dateFrom').value = weekAgo.toISOString().split('T')[0];
            document.getElementById('dateTo').value = today.toISOString().split('T')[0];
            
            // Submit form to apply cleared filters
            filterForm.submit();
        });
    }
    
    // Reset filters (from empty state)
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            window.location.href = window.location.pathname;
        });
    }
    
    // Auto-refresh functionality
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            refreshLogs();
        }, 30000); // 30 seconds
    }
    
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    }
    
    function refreshLogs() {
        if (!isRefreshEnabled) return;
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        // Add loading indicator
        logsContainer.style.position = 'relative';
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="spinner"></div>';
        logsContainer.appendChild(loadingOverlay);
        
        fetch(`/dashboard/audit-logs/refresh?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateLogsTable(data.logs);
                updateStats(data.stats);
            }
        })
        .catch(error => {
            console.error('Refresh error:', error);
        })
        .finally(() => {
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        });
    }
    
    function updateLogsTable(logs) {
        const tbody = document.getElementById('logsTableBody');
        if (!tbody || !logs) return;
        
        // Clear existing rows
        tbody.innerHTML = '';
        
        // Add new rows
        logs.forEach(log => {
            const row = createLogRow(log);
            tbody.appendChild(row);
        });
    }
    
    function createLogRow(log) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="log-user">
                    <div class="user-avatar">
                        ${log.user ? (log.user.first_name.charAt(0) + log.user.last_name.charAt(0)) : 'S'}
                    </div>
                    <div class="user-info">
                        <div class="user-name">${log.user ? (log.user.first_name + ' ' + log.user.last_name) : 'System'}</div>
                        <div class="user-role">${log.user && log.user.role ? log.user.role.display_name : 'Automated'}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="action-badge ${log.action_type}">
                    ${getActionIcon(log.action)}
                    ${formatAction(log.action)}
                </span>
            </td>
            <td>
                <div class="log-details">
                    ${log.description}
                    ${log.target_type && log.target_id ? `<div style="margin-top: 0.25rem; font-size: 0.75rem; color: #9ca3af;">Target: <span class="highlight">${log.target_type} #${log.target_id}</span></div>` : ''}
                </div>
            </td>
            <td>
                <span class="status-badge ${log.status || 'success'}">
                    ${getStatusIcon(log.status || 'success')}
                    ${formatStatus(log.status || 'success')}
                </span>
            </td>
            <td>
                <div class="timestamp">
                    <code style="font-size: 0.75rem; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                        ${log.ip_address || 'N/A'}
                    </code>
                </div>
            </td>
            <td>
                <div class="timestamp">
                    <div class="timestamp-main">${formatDate(log.created_at)}</div>
                    <div class="timestamp-sub">${formatTime(log.created_at)}</div>
                </div>
            </td>
        `;
        return row;
    }
    
    function getActionIcon(action) {
        const icons = {
            'user_login': '<path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3z"/>',
            'user_logout': '<path d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1z"/>',
            'user_created': '<path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"/>',
            'user_updated': '<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793z"/>',
            'user_deleted': '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>'
        };
        
        return `<svg class="action-icon" fill="currentColor" viewBox="0 0 20 20">${icons[action] || '<path d="M10 18a8 8 0 100-16 8 8 0 000 16z"/>'}</svg>`;
    }
    
    function getStatusIcon(status) {
        const icons = {
            'success': '<path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>',
            'warning': '<path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z"/>',
            'error': '<path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>'
        };
        
        return `<svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">${icons[status]}</svg>`;
    }
    
    function formatAction(action) {
        return action.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
    
    function formatStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }
    
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
    
    function formatTime(dateString) {
        return new Date(dateString).toLocaleTimeString('en-US', { hour12: false });
    }
    
    function updateStats(stats) {
        // Update statistics bar
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"] .stat-value`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    }
    
    // Toggle refresh
    toggleRefreshBtn.addEventListener('click', function() {
        isRefreshEnabled = !isRefreshEnabled;
        
        if (isRefreshEnabled) {
            startAutoRefresh();
            this.innerHTML = `
                <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                </svg>
                Pause
            `;
            document.querySelector('.refresh-indicator').style.display = 'block';
        } else {
            stopAutoRefresh();
            this.innerHTML = `
                <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1z"/>
                </svg>
                Resume
            `;
            document.querySelector('.refresh-indicator').style.display = 'none';
        }
    });
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Stop refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else if (isRefreshEnabled) {
            startAutoRefresh();
        }
    });
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        stopAutoRefresh();
    });
});
</script>
@endpush