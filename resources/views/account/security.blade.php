@extends('layouts.dashboard')

@section('title', 'Security - My Account')

@push('styles')
<style>
    .security-header {
        margin-bottom: 1.5rem;
    }
    
    .security-title {
        font-size: 1.5rem;
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .security-description {
        color: #6b7280;
    }
    
    .security-card {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .security-card-content {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .security-icon {
        width: 4rem;
        height: 4rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .security-icon.orange {
        background-color: #fff7ed;
    }
    
    .security-icon-inner {
        width: 2rem;
        height: 2rem;
        color: var(--swa-orange);
    }
    
    .security-body {
        flex: 1;
    }
    
    .security-card-title {
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .security-card-text {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    
    .recovery-section {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .recovery-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .recovery-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .recovery-description {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .recovery-body {
        padding: 1.5rem;
    }
    
    .recovery-item {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .recovery-item:last-child {
        margin-bottom: 0;
    }
    
    .recovery-item-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .recovery-item-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .recovery-item-icon.red {
        background-color: #fef2f2;
        color: #dc2626;
    }
    
    .recovery-item-icon.orange {
        background-color: #fff7ed;
        color: var(--swa-orange);
    }
    
    .recovery-item-content h4 {
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    
    .recovery-item-content p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
    
    .recovery-item-action {
        color: #9ca3af;
    }
    
    .auth-section {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
    }
    
    .auth-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .auth-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .auth-description {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .auth-body {
        padding: 1.5rem;
    }
    
    .auth-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background-color: #f9fafb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .auth-item:last-child {
        margin-bottom: 0;
    }
    
    .auth-item-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .auth-item-icon {
        width: 1.25rem;
        height: 1.25rem;
        color: #6b7280;
    }
    
    .auth-item-content h4 {
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    
    .auth-item-content p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
</style>
@endpush

@section('account-content')
<div class="security-header">
    <h1 class="security-title">Security</h1>
    <p class="security-description">Secure your account by managing consents, sessions, and security settings</p>
</div>

<!-- Change Password Section -->
<div class="security-card">
    <div class="security-card-content">
        <div class="security-icon orange">
            <svg class="security-icon-inner" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="security-body">
            <h3 class="security-card-title">Change Password</h3>
            <p class="security-card-text">
                Update your password regularly and make sure it's unique from other passwords you use.
            </p>
            <button type="button" class="btn btn-outline-orange" onclick="changePassword()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="mr-2">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                Change your password
            </button>
        </div>
    </div>
</div>

<!-- Account Recovery Section -->
<div class="recovery-section">
    <div class="recovery-header">
        <h2 class="recovery-title">Account Recovery</h2>
        <p class="recovery-description">
            Manage recovery information that we can use to help you recover your username or password
        </p>
    </div>
    <div class="recovery-body">
        <div class="recovery-item">
            <div class="recovery-item-left">
                <div class="recovery-item-icon red">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                    </svg>
                </div>
                <div class="recovery-item-content">
                    <h4>Security questions</h4>
                    <p>Add or update account recovery challenge questions</p>
                </div>
            </div>
            <svg class="recovery-item-action" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
        </div>
        
        <div class="recovery-item">
            <div class="recovery-item-left">
                <div class="recovery-item-icon orange">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                </div>
                <div class="recovery-item-content">
                    <h4>Email recovery</h4>
                    <p>Add or update recovery email address</p>
                </div>
            </div>
            <svg class="recovery-item-action" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Additional Authentication Section -->
<div class="auth-section">
    <div class="auth-header">
        <h2 class="auth-title">Additional Authentication</h2>
        <p class="auth-description">
            Configure additional authentications to sign in easily or to add an extra layer of security to your account.
        </p>
    </div>
    <div class="auth-body">
        <div class="auth-item">
            <div class="auth-item-left">
                <svg class="auth-item-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <div class="auth-item-content">
                    <h4>Two-Factor Authentication</h4>
                    <p>Add an extra layer of security to your account</p>
                </div>
            </div>
            <span class="badge badge-secondary">Not Configured</span>
        </div>
        
        <div class="auth-item">
            <div class="auth-item-left">
                <svg class="auth-item-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9zM13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                <div class="auth-item-content">
                    <h4>Backup Codes</h4>
                    <p>Generate backup codes for account recovery</p>
                </div>
            </div>
            <span class="badge badge-secondary">Not Generated</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changePassword() {
    alert('Change password functionality would open a modal or redirect to a password change form.');
}

// Add click handlers for recovery items
document.addEventListener('DOMContentLoaded', function() {
    const recoveryItems = document.querySelectorAll('.recovery-item');
    
    recoveryItems.forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function() {
            const title = this.querySelector('h4').textContent;
            alert(`${title} configuration would open here.`);
        });
    });
    
    const authItems = document.querySelectorAll('.auth-item');
    
    authItems.forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function() {
            const title = this.querySelector('h4').textContent;
            alert(`${title} setup would open here.`);
        });
    });
});
</script>
@endpush