@extends('layouts.dashboard')

@section('title', 'Overview - My Account')

@push('styles')
<style>
    .overview-header {
        margin-bottom: 1.5rem;
    }
    
    .overview-title {
        font-size: 1.5rem;
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .overview-description {
        color: #6b7280;
    }
    
    .overview-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .overview-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .overview-card {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
    
    .card-content {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .card-icon {
        width: 4rem;
        height: 4rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .card-icon.red {
        background-color: #fef2f2;
    }
    
    .card-icon.blue {
        background-color: #eff6ff;
    }
    
    .card-icon.orange {
        background-color: #fff7ed;
    }
    
    .card-icon-inner {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card-icon-inner.red {
        background-color: #ef4444;
        color: white;
    }
    
    .card-icon-inner.blue {
        background-color: var(--swa-blue);
        color: white;
    }
    
    .card-icon-inner.orange {
        background-color: #f97316;
        color: white;
    }
    
    .card-body {
        flex: 1;
    }
    
    .card-title {
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .card-text {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }
    
    .progress-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    
    .progress {
        flex: 1;
        height: 0.5rem;
        background-color: #e5e7eb;
        border-radius: 9999px;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        background-color: var(--swa-blue);
        transition: width 0.3s ease;
    }
    
    .progress-text {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .alert-text {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .alert-icon {
        width: 1rem;
        height: 1rem;
    }
    
    .session-info {
        background-color: #f9fafb;
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .session-device {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    
    .session-device-icon {
        width: 1rem;
        height: 1rem;
        color: #6b7280;
    }
    
    .session-device-name {
        font-weight: 500;
        font-size: 0.875rem;
        color: #111827;
    }
    
    .session-time {
        font-size: 0.75rem;
        color: #6b7280;
    }
</style>
@endpush

@section('account-content')
<div class="overview-header">
    <h1 class="overview-title">Welcome, {{ $user['given_name'] ?? $user['name'] ?? 'User' }}!</h1>
    <p class="overview-description">Manage your personal information, account security, and privacy settings</p>
</div>

<div class="overview-grid">
    <!-- Complete Profile Card -->
    <div class="overview-card">
        <div class="card-content">
            <div class="card-icon red">
                <div class="card-icon-inner red">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                </div>
            </div>
            <div class="card-body">
                <h3 class="card-title">Complete your profile</h3>
                <p class="card-text">Your profile completion is at {{ $profileCompletion }}%</p>
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $profileCompletion }}%"></div>
                    </div>
                    <span class="progress-text">{{ $profileCompletion }}%</span>
                </div>
                <p class="alert-text">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    {{ 6 - floor($profileCompletion / 100 * 6) }} out of 6 optional fields completed
                </p>
                <a href="{{ route('account.personal-info') }}" class="btn btn-outline-orange">
                    Complete profile
                </a>
            </div>
        </div>
    </div>

    <!-- Active Sessions Card -->
    <div class="overview-card">
        <div class="card-content">
            <div class="card-icon blue">
                <div class="card-icon-inner blue">
                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
            </div>
            <div class="card-body">
                <h3 class="card-title">Active Sessions</h3>
                <p class="card-text">You are currently logged in from the following device.</p>
                <div class="session-info">
                    <div class="session-device">
                        <svg class="session-device-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        <span class="session-device-name">Chrome on Windows</span>
                    </div>
                    <p class="session-time">Last accessed a few seconds ago</p>
                </div>
                <button type="button" class="btn btn-outline-orange">
                    Manage account activity
                </button>
            </div>
        </div>
    </div>

    <!-- Account Security Card -->
    <div class="overview-card">
        <div class="card-content">
            <div class="card-icon orange">
                <div class="card-icon-inner orange">
                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="card-body">
                <h3 class="card-title">Account Security</h3>
                <p class="card-text">Settings and recommendations to help you keep your account secure</p>
                <a href="{{ route('account.security') }}" class="btn btn-outline-orange">
                    Update account security
                </a>
            </div>
        </div>
    </div>

    <!-- Control Consents Card -->
    <div class="overview-card">
        <div class="card-content">
            <div class="card-icon blue">
                <div class="card-icon-inner blue">
                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM15 8a1 1 0 011-1V4a1 1 0 10-2 0v3a1 1 0 011 1zm1 3.268V16a1 1 0 11-2 0v-4.732a2 2 0 002 0z"/>
                    </svg>
                </div>
            </div>
            <div class="card-body">
                <h3 class="card-title">Control Consents</h3>
                <p class="card-text">Control the data you want to share with applications</p>
                <button type="button" class="btn btn-outline-orange">
                    Manage consents
                </button>
            </div>
        </div>
    </div>
</div>
@endsection