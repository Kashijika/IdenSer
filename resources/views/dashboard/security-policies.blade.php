@extends('layouts.dashboard')

@section('title', 'Security Policies')

@push('styles')
<style>
    .security-container {
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
    
    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .card-icon {
        width: 3rem;
        height: 3rem;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    
    .card-description {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .form-section {
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-icon {
        width: 1.25rem;
        height: 1.25rem;
        color: #1e3a8a;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 500;
        color: #374151;
    }
    
    .form-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .form-input,
    .form-select {
        width: 100%;
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
    
    .toggle-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .toggle {
        position: relative;
        width: 3rem;
        height: 1.5rem;
        background: #d1d5db;
        border-radius: 9999px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .toggle.active {
        background: #1e3a8a;
    }
    
    .toggle-slider {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 1.25rem;
        height: 1.25rem;
        background: white;
        border-radius: 50%;
        transition: transform 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .toggle.active .toggle-slider {
        transform: translateX(1.5rem);
    }
    
    .toggle-label {
        font-weight: 500;
        color: #374151;
    }
    
    .policy-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    
    .policy-status.enabled {
        background: #d1fae5;
        color: #065f46;
    }
    
    .policy-status.disabled {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .policy-status.warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .recommendations {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .recommendations-title {
        font-weight: 600;
        color: #0c4a6e;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .recommendations-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .recommendations-list li {
        padding: 0.25rem 0;
        color: #0369a1;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .requirements-list {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .requirements-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .requirements-list ul {
        margin: 0;
        padding-left: 1.25rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .requirements-list li {
        margin-bottom: 0.25rem;
    }
    
    .save-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    .complexity-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .complexity-bar {
        flex: 1;
        height: 0.5rem;
        background: #e5e7eb;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    
    .complexity-fill {
        height: 100%;
        transition: width 0.3s ease;
        border-radius: 0.25rem;
    }
    
    .complexity-fill.weak {
        background: #dc2626;
        width: 25%;
    }
    
    .complexity-fill.fair {
        background: #f59e0b;
        width: 50%;
    }
    
    .complexity-fill.good {
        background: #059669;
        width: 75%;
    }
    
    .complexity-fill.strong {
        background: #10b981;
        width: 100%;
    }
    
    .complexity-text {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        min-width: 4rem;
    }
</style>
@endpush

@section('content')
<div class="security-container">
    <div class="page-header">
        <h1 class="page-title">Security Policies</h1>
        <p class="page-description">Configure security settings and password policies for the organization</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20">
            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-error">
        <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20">
            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
        </svg>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 0; list-style: disc; margin-left: 1.25rem;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form id="securityPolicyForm" method="POST" action="{{ route('dashboard.security-policies.update') }}">
        @csrf
        @method('PUT')

        <!-- Password Security -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title">Password Security</h2>
                    <p class="card-description">Configure password complexity requirements and policies</p>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Password Complexity
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <div class="toggle-container">
                            <div class="toggle {{ $securityPolicy->password_complexity_enabled ? 'active' : '' }}" 
                                 data-toggle="password_complexity_enabled">
                                <div class="toggle-slider"></div>
                            </div>
                            <label class="toggle-label">Enable Password Complexity Requirements</label>
                        </div>
                        <input type="hidden" name="password_complexity_enabled" 
                               value="{{ $securityPolicy->password_complexity_enabled ? '1' : '0' }}">
                        <p class="form-description">
                            Enforce strong password requirements for all user accounts
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="min_password_length">Minimum Password Length</label>
                        <input type="number" id="min_password_length" name="min_password_length" 
                               class="form-input" min="6" max="32" 
                               value="{{ old('min_password_length', $securityPolicy->min_password_length) }}"
                               {{ !$securityPolicy->password_complexity_enabled ? 'disabled' : '' }}>
                        <p class="form-description">Minimum number of characters required (6-32)</p>
                        
                        <div class="complexity-indicator">
                            <div class="complexity-bar">
                                <div class="complexity-fill" id="complexityFill"></div>
                            </div>
                            <span class="complexity-text" id="complexityText">Good</span>
                        </div>
                    </div>
                </div>

                <div class="requirements-list" id="passwordRequirements" 
                     style="{{ !$securityPolicy->password_complexity_enabled ? 'display: none;' : '' }}">
                    <div class="requirements-title">Current Password Requirements:</div>
                    <ul>
                        <li>At least <span id="minLenSpan">{{ $securityPolicy->min_password_length }}</span> characters long</li>
                        <li>At least one uppercase letter (A-Z)</li>
                        <li>At least one lowercase letter (a-z)</li>
                        <li>At least one number (0-9)</li>
                        <li>At least one special character (!@#$%^&*)</li>
                        <li>Cannot contain username or email</li>
                        <li>Cannot be one of the last 5 passwords used</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Multi-Factor Authentication -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2L3 7v3c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-7-5z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title">Multi-Factor Authentication</h2>
                    <p class="card-description">Enhance security with additional authentication factors</p>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <div class="toggle-container">
                            <div class="toggle {{ $securityPolicy->mfa_enabled ? 'active' : '' }}" 
                                 data-toggle="mfa_enabled">
                                <div class="toggle-slider"></div>
                            </div>
                            <label class="toggle-label">Require Multi-Factor Authentication</label>
                        </div>
                        <input type="hidden" name="mfa_enabled" 
                               value="{{ $securityPolicy->mfa_enabled ? '1' : '0' }}">
                        <p class="form-description">
                            Users must provide a second factor (SMS, authenticator app) when signing in
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mfa_methods">Allowed MFA Methods</label>
                        <select id="mfa_methods" name="mfa_methods[]" class="form-select" multiple
                                {{ !$securityPolicy->mfa_enabled ? 'disabled' : '' }}>
                            <option value="sms" {{ in_array('sms', $securityPolicy->mfa_methods ?? []) ? 'selected' : '' }}>
                                SMS Text Message
                            </option>
                            <option value="email" {{ in_array('email', $securityPolicy->mfa_methods ?? []) ? 'selected' : '' }}>
                                Email Verification
                            </option>
                            <option value="totp" {{ in_array('totp', $securityPolicy->mfa_methods ?? []) ? 'selected' : '' }}>
                                Authenticator App (Google Authenticator, Authy)
                            </option>
                            <option value="backup_codes" {{ in_array('backup_codes', $securityPolicy->mfa_methods ?? []) ? 'selected' : '' }}>
                                Backup Codes
                            </option>
                        </select>
                        <p class="form-description">Hold Ctrl/Cmd to select multiple methods</p>
                    </div>
                </div>

                <div class="recommendations" id="mfaRecommendations" 
                     style="{{ !$securityPolicy->mfa_enabled ? 'display: none;' : '' }}">
                    <div class="recommendations-title">
                        <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Security Recommendations
                    </div>
                    <ul class="recommendations-list">
                        <li>
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                            Authenticator apps provide the highest security
                        </li>
                        <li>
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                            Always enable backup codes for account recovery
                        </li>
                        <li>
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                            Consider requiring MFA for admin and HR roles only initially
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Session Management -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title">Session Management</h2>
                    <p class="card-description">Control user session timeouts and security</p>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="session_timeout">Session Timeout</label>
                        <select id="session_timeout" name="session_timeout" class="form-select">
                            <option value="15" {{ $securityPolicy->session_timeout == 15 ? 'selected' : '' }}>
                                15 minutes (High Security)
                            </option>
                            <option value="30" {{ $securityPolicy->session_timeout == 30 ? 'selected' : '' }}>
                                30 minutes (Medium Security)
                            </option>
                            <option value="60" {{ $securityPolicy->session_timeout == 60 ? 'selected' : '' }}>
                                1 hour (Standard)
                            </option>
                            <option value="120" {{ $securityPolicy->session_timeout == 120 ? 'selected' : '' }}>
                                2 hours (Extended)
                            </option>
                            <option value="480" {{ $securityPolicy->session_timeout == 480 ? 'selected' : '' }}>
                                8 hours (Full Workday)
                            </option>
                        </select>
                        <p class="form-description">
                            Users will be automatically logged out after this period of inactivity
                        </p>
                    </div>

                    <div class="form-group">
                        <div class="toggle-container">
                            <div class="toggle {{ $securityPolicy->concurrent_sessions_enabled ? 'active' : '' }}" 
                                 data-toggle="concurrent_sessions_enabled">
                                <div class="toggle-slider"></div>
                            </div>
                            <label class="toggle-label">Limit Concurrent Sessions</label>
                        </div>
                        <input type="hidden" name="concurrent_sessions_enabled" 
                               value="{{ $securityPolicy->concurrent_sessions_enabled ? '1' : '0' }}">
                        <p class="form-description">
                            Limit the number of active sessions per user account
                        </p>
                    </div>
                </div>

                <div class="form-group" id="maxSessionsGroup" 
                     style="{{ !$securityPolicy->concurrent_sessions_enabled ? 'display: none;' : '' }}">
                    <label class="form-label" for="max_concurrent_sessions">Maximum Concurrent Sessions</label>
                    <input type="number" id="max_concurrent_sessions" name="max_concurrent_sessions" 
                           class="form-input" min="1" max="10" 
                           value="{{ old('max_concurrent_sessions', $securityPolicy->max_concurrent_sessions ?? 3) }}"
                           {{ !$securityPolicy->concurrent_sessions_enabled ? 'disabled' : '' }}>
                    <p class="form-description">Maximum number of simultaneous login sessions (1-10)</p>
                </div>
            </div>
        </div>

        <!-- Account Lockout -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-2a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title">Account Lockout Policy</h2>
                    <p class="card-description">Protect against brute force attacks</p>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <div class="toggle-container">
                            <div class="toggle {{ $securityPolicy->account_lockout_enabled ? 'active' : '' }}" 
                                 data-toggle="account_lockout_enabled">
                                <div class="toggle-slider"></div>
                            </div>
                            <label class="toggle-label">Enable Account Lockout</label>
                        </div>
                        <input type="hidden" name="account_lockout_enabled" 
                               value="{{ $securityPolicy->account_lockout_enabled ? '1' : '0' }}">
                        <p class="form-description">
                            Temporarily lock accounts after multiple failed login attempts
                        </p>
                    </div>

                    <div class="form-group" id="lockoutSettings" 
                         style="{{ !$securityPolicy->account_lockout_enabled ? 'display: none;' : '' }}">
                        <label class="form-label" for="max_login_attempts">Failed Attempts Before Lockout</label>
                        <select id="max_login_attempts" name="max_login_attempts" class="form-select"
                                {{ !$securityPolicy->account_lockout_enabled ? 'disabled' : '' }}>
                            <option value="3" {{ ($securityPolicy->max_login_attempts ?? 5) == 3 ? 'selected' : '' }}>
                                3 attempts (Strict)
                            </option>
                            <option value="5" {{ ($securityPolicy->max_login_attempts ?? 5) == 5 ? 'selected' : '' }}>
                                5 attempts (Standard)
                            </option>
                            <option value="10" {{ ($securityPolicy->max_login_attempts ?? 5) == 10 ? 'selected' : '' }}>
                                10 attempts (Lenient)
                            </option>
                        </select>
                        <p class="form-description">
                            Account will be locked after this many consecutive failed login attempts
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Security Status -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title">Security Status Overview</h2>
                    <p class="card-description">Current security policy status and recommendations</p>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid">
                    <div>
                        <h4 style="font-weight: 600; color: #374151; margin-bottom: 1rem;">Active Policies</h4>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="policy-status {{ $securityPolicy->password_complexity_enabled ? 'enabled' : 'disabled' }}">
                                <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                    @if($securityPolicy->password_complexity_enabled)
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    @else
                                    <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                    @endif
                                </svg>
                                Password Complexity: {{ $securityPolicy->password_complexity_enabled ? 'Enabled' : 'Disabled' }}
                            </div>
                            
                            <div class="policy-status {{ $securityPolicy->mfa_enabled ? 'enabled' : 'warning' }}">
                                <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                    @if($securityPolicy->mfa_enabled)
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    @else
                                    <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    @endif
                                </svg>
                                Multi-Factor Auth: {{ $securityPolicy->mfa_enabled ? 'Enabled' : 'Recommended' }}
                            </div>
                            
                            <div class="policy-status enabled">
                                <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                                Session Timeout: {{ $securityPolicy->session_timeout }} minutes
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 style="font-weight: 600; color: #374151; margin-bottom: 1rem;">Security Score</h4>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; font-weight: 600; color: #1e3a8a; margin-bottom: 0.5rem;">
                                {{ $securityScore }}%
                            </div>
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                                @if($securityScore >= 90)
                                    Excellent Security
                                @elseif($securityScore >= 75)
                                    Good Security
                                @elseif($securityScore >= 60)
                                    Fair Security
                                @else
                                    Needs Improvement
                                @endif
                            </div>
                            <div class="complexity-bar" style="width: 100%; max-width: 200px; margin: 0 auto;">
                                <div class="complexity-fill {{ $securityScore >= 75 ? 'good' : ($securityScore >= 50 ? 'fair' : 'weak') }}" 
                                     style="width: {{ $securityScore }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Actions -->
        <div class="save-actions">
            <button type="button" class="btn btn-secondary" id="resetBtn">Reset to Defaults</button>
            <button type="submit" class="btn btn-primary" id="saveBtn">
                <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"/>
                </svg>
                Save Security Policies
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('securityPolicyForm');
    const toggles = document.querySelectorAll('.toggle');
    const minPasswordLength = document.getElementById('min_password_length');
    const complexityFill = document.getElementById('complexityFill');
    const complexityText = document.getElementById('complexityText');
    const minLenSpan = document.getElementById('minLenSpan');
    
    // Toggle functionality
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const isActive = this.classList.contains('active');
            const toggleName = this.getAttribute('data-toggle');
            const hiddenInput = document.querySelector(`input[name="${toggleName}"]`);
            
            if (isActive) {
                this.classList.remove('active');
                hiddenInput.value = '0';
            } else {
                this.classList.add('active');
                hiddenInput.value = '1';
            }
            
            // Handle dependent fields
            handleToggleDependencies(toggleName, !isActive);
        });
    });
    
    // Handle toggle dependencies
    function handleToggleDependencies(toggleName, isEnabled) {
        switch(toggleName) {
            case 'password_complexity_enabled':
                const passwordFields = document.getElementById('min_password_length');
                const requirements = document.getElementById('passwordRequirements');
                
                if (passwordFields) {
                    passwordFields.disabled = !isEnabled;
                }
                if (requirements) {
                    requirements.style.display = isEnabled ? 'block' : 'none';
                }
                break;
                
            case 'mfa_enabled':
                const mfaMethods = document.getElementById('mfa_methods');
                const mfaRecommendations = document.getElementById('mfaRecommendations');
                
                if (mfaMethods) {
                    mfaMethods.disabled = !isEnabled;
                }
                if (mfaRecommendations) {
                    mfaRecommendations.style.display = isEnabled ? 'block' : 'none';
                }
                break;
                
            case 'concurrent_sessions_enabled':
                const maxSessions = document.getElementById('max_concurrent_sessions');
                const maxSessionsGroup = document.getElementById('maxSessionsGroup');
                
                if (maxSessions) {
                    maxSessions.disabled = !isEnabled;
                }
                if (maxSessionsGroup) {
                    maxSessionsGroup.style.display = isEnabled ? 'block' : 'none';
                }
                break;
                
            case 'account_lockout_enabled':
                const lockoutSettings = document.getElementById('lockoutSettings');
                const maxAttempts = document.getElementById('max_login_attempts');
                
                if (lockoutSettings) {
                    lockoutSettings.style.display = isEnabled ? 'block' : 'none';
                }
                if (maxAttempts) {
                    maxAttempts.disabled = !isEnabled;
                }
                break;
        }
    }
    
    // Password complexity indicator
    if (minPasswordLength) {
        minPasswordLength.addEventListener('input', function() {
            const length = parseInt(this.value);
            updateComplexityIndicator(length);
            if (minLenSpan) {
                minLenSpan.textContent = length;
            }
        });
    }
    
    function updateComplexityIndicator(length) {
        if (!complexityFill || !complexityText) return;
        
        let level, text;
        
        if (length < 8) {
            level = 'weak';
            text = 'Weak';
        } else if (length < 12) {
            level = 'fair';
            text = 'Fair';
        } else if (length < 16) {
            level = 'good';
            text = 'Good';
        } else {
            level = 'strong';
            text = 'Strong';
        }
        
        complexityFill.className = `complexity-fill ${level}`;
        complexityText.textContent = text;
    }
    
    // Reset button
    document.getElementById('resetBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to reset all security policies to default values? This will override any current settings.')) {
            // Reset to default values
            document.querySelector('input[name="password_complexity_enabled"]').value = '1';
            document.querySelector('input[name="min_password_length"]').value = '8';
            document.querySelector('input[name="mfa_enabled"]').value = '0';
            document.querySelector('input[name="session_timeout"]').value = '60';
            document.querySelector('input[name="concurrent_sessions_enabled"]').value = '0';
            document.querySelector('input[name="max_concurrent_sessions"]').value = '3';
            document.querySelector('input[name="account_lockout_enabled"]').value = '1';
            document.querySelector('input[name="max_login_attempts"]').value = '5';
            
            // Update toggles
            toggles.forEach(toggle => {
                const toggleName = toggle.getAttribute('data-toggle');
                const hiddenInput = document.querySelector(`input[name="${toggleName}"]`);
                
                if (['password_complexity_enabled', 'account_lockout_enabled'].includes(toggleName)) {
                    toggle.classList.add('active');
                    hiddenInput.value = '1';
                    handleToggleDependencies(toggleName, true);
                } else {
                    toggle.classList.remove('active');
                    hiddenInput.value = '0';
                    handleToggleDependencies(toggleName, false);
                }
            });
            
            // Update form fields
            document.getElementById('min_password_length').value = '8';
            document.getElementById('session_timeout').value = '60';
            document.getElementById('max_concurrent_sessions').value = '3';
            document.getElementById('max_login_attempts').value = '5';
            
            // Clear MFA methods selection
            const mfaSelect = document.getElementById('mfa_methods');
            Array.from(mfaSelect.options).forEach(option => option.selected = false);
            
            updateComplexityIndicator(8);
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const saveBtn = document.getElementById('saveBtn');
        showLoading(saveBtn, true);
        
        // Form will submit normally, but we show loading state
        setTimeout(() => {
            if (saveBtn) {
                showLoading(saveBtn, false);
            }
        }, 1000);
    });
    
    // Initialize complexity indicator
    if (minPasswordLength) {
        updateComplexityIndicator(parseInt(minPasswordLength.value));
    }
    
    // Show/hide dependent sections on page load
    toggles.forEach(toggle => {
        const toggleName = toggle.getAttribute('data-toggle');
        const isEnabled = toggle.classList.contains('active');
        handleToggleDependencies(toggleName, isEnabled);
    });
});
</script>
@endpush