@extends('layouts.app')

@section('title', 'Login - SWA Media Account Portal')

@push('styles')
<style>
    .login-container {
        min-height: 100vh;
        background-color: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .login-card {
        width: 100%;
        max-width: 1200px; /* max lebar card */
        background: white;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        display: flex;
        min-height: 700px;
    }

    
    .login-left {
        width: 60%;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        position: relative;
        overflow: hidden;
    }
    
    .login-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.2);
    }
    
    .login-left::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: 
            radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(255,255,255,0.05) 0%, transparent 50%);
    }
    
    .logo-container {
        position: relative;
        z-index: 10;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .logo-backdrop {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        border-radius: 1.5rem;
        padding: 3rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .logo-backdrop img {
        height: 6rem;
        width: auto;
    }
    
    .login-right {
        width: 40%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .login-form-container {
        width: 100%;
        max-width: 20rem;
    }
    
    .form-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .form-header img {
        height: 4rem;
        margin: 0 auto 0.75rem;
        display: block;
    }
    
    .form-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .form-description {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .sso-section {
        margin-bottom: 1.25rem;
    }
    
    .sso-title {
        text-align: center;
        color: #374151;
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    /* BARU: Container untuk menampung tombol SSO */
    .sso-buttons-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .sso-button {
        width: 100%;
        height: 2.75rem;
        color: white;
        border: none;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .sso-button:hover {
        transform: translateY(-1px);
    }
    
    .sso-button:active {
        transform: translateY(0);
    }
    
    .sso-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .sso-icon { /* Menggantikan .google-icon agar lebih generik */
        width: 1rem;
        height: 1rem;
        fill: currentColor;
    }

    /* Warna Tombol Google (default) */
    #ssoButton {
        background-color: #4285f4;
    }
    #ssoButton:hover {
        background-color: #3367d6;
    }

    /* BARU: Warna Tombol WSO2 */
    #wso2Button {
        background-color: #FF7300; /* Warna oranye WSO2 */
    }
    #wso2Button:hover {
        background-color: #e66800; /* Oranye lebih gelap */
    }
    
    .divider {
        position: relative;
        margin-bottom: 1.25rem;
    }
    
    .divider::before {
        content: '';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
    }
    
    .divider::before {
        width: 100%;
        height: 1px;
        background-color: #d1d5db;
    }
    
    .divider-text {
        position: relative;
        display: flex;
        justify-content: center;
        font-size: 0.875rem;
    }
    
    .divider-text span {
        padding: 0 0.5rem;
        background-color: white;
        color: #6b7280;
    }
    
    .company-card {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .company-header {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .company-title {
        font-size: 1rem;
        text-align: center;
        color: #111827;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .company-description {
        text-align: center;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .company-body {
        padding: 0.75rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        height: 2.5rem;
        padding: 0 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    
    .form-options a {
        color: #1e3a8a;
        text-decoration: none;
    }
    
    .form-options a:hover {
        text-decoration: underline;
    }
    
    .btn-primary {
        width: 100%;
        height: 2.5rem;
        background-color: #1e3a8a;
        color: white;
        border: none;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        background-color: #1e40af;
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .footer {
        margin-top: 1.5rem;
        text-align: center;
    }
    
    .footer-links {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .language-selector {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: #6b7280;
    }
    
    .language-icon, .security-icon {
        width: 0.75rem;
        height: 0.75rem;
    }
    
    .footer-links a {
        color: #1e3a8a;
        text-decoration: none;
    }
    
    .footer-links a:hover {
        text-decoration: underline;
    }
    
    .security-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .success-message, .error-message {
        padding: 0.75rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    .success-message {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    
    .error-message {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .hidden {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-left">
            <div class="logo-container">
                <div class="logo-backdrop">
                    <img src="{{ asset('images/swa-logo.png') }}" alt="SWA Media" />
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-form-container">
                <div class="form-header">
                    <img src="{{ asset('images/swa-logo.png') }}" alt="SWA Media" />
                    <h2 class="form-title">Hi, Welcome to SWA Media Account Portal</h2>
                    <p class="form-description">Enter your details to log in your account</p>
                </div>

                <div id="successMessage" class="success-message hidden"></div>
                <div id="errorMessage" class="error-message hidden"></div>

                <div class="sso-section">
                    <p class="sso-title">Login with your SSO account</p>
                    
                    <div class="sso-buttons-container">
                        <button type="button" id="wso2Button" class="sso-button">
                            <svg class="sso-icon" viewBox="0 0 256 256" xmlns="https://wso2.cachefly.net/wso2/sites/images/brand/downloads/wso2-logo.svg">
                                <path d="M128 0C57.3 0 0 57.3 0 128s57.3 128 128 128 128-57.3 128-128S198.7 0 128 0zM89.6 89.6a19.2 19.2 0 110 27.2 19.2 19.2 0 010-27.2zm38.4 76.8a19.2 19.2 0 110-27.2 19.2 19.2 0 010 27.2zm38.4-38.4a19.2 19.2 0 110-27.2 19.2 19.2 0 010 27.2z"/>
                            </svg>
                            <span id="wso2ButtonText">Sign in with WSO2</span>
                        </button>
                    </div>
                </div>

                <div class="divider">
                    <div class="divider-text">
                        <span>or</span>
                    </div>
                </div>

                <div class="company-card">
                    <div class="company-header">
                        <h3 class="company-title">Company Account</h3>
                        <p class="company-description">Sign in with your company credentials</p>
                    </div>
                    <div class="company-body">
                        <form id="loginForm">
                            @csrf
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Enter your password" required>
                            </div>
                            <div class="form-options">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="remember">
                                    <span>Remember me</span>
                                </label>
                                {{-- <a href="{{ route('password.request') }}">Forgot password?</a> --}}
                            </div>
                            <button type="submit" id="loginButton" class="btn-primary">
                                <span id="loginButtonText">Sign In</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="footer">
                    <div class="footer-links">
                        <div class="language-selector">
                            <svg class="language-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm8 10c0 1.3-.3 2.53-.83 3.62L15 11.45V9.5c0-.55-.45-1-1-1h-2c-.55 0-1 .45-1 1v2c0 .55.45 1 1 1h.5l1.17 2.34c-.75.46-1.63.66-2.67.66-2.76 0-5-2.24-5-5s2.24-5 5-5c.87 0 1.67.22 2.38.62L15.17 3.38C13.85 2.52 12.47 2 10 2 5.58 2 2 5.58 2 10s3.58 8 8 8c1.4 0 2.7-.36 3.83-1H16c1.1 0 2-.9 2-2v-3c0-.34-.11-.65-.29-.91zM7 9H5c-.55 0-1-.45-1-1s.45-1 1-1h2c.55 0 1 .45 1 1s-.45 1-1 1z"/>
                            </svg>
                            <span>English (en)</span>
                            <svg class="language-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                            </svg>
                        </div>
                        <a href="{{ route('privacy') }}">Privacy Policy</a>
                    </div>
                    <div class="security-note">
                        <svg class="security-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                        </svg>
                        <span>Secure connection</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    // --- WSO2 SSO Logic ---
    const wso2Button = document.getElementById('wso2Button');
    const wso2ButtonText = document.getElementById('wso2ButtonText');
    
    wso2Button.addEventListener('click', async function() {
        wso2Button.disabled = true;
        wso2ButtonText.textContent = 'Connecting...';
        
        try {
            // Redirect to WSO2 SSO
            window.location.href = '{{ route("auth.sso.wso2") }}';
        } catch (error) {
            console.error('WSO2 SSO error:', error);
            showMessage(errorMessage, 'WSO2 connection error. Please try again.', false);
        } finally {
            wso2Button.disabled = false;
            wso2ButtonText.textContent = 'Sign in with WSO2';
        }
    });

    // --- Logika Form Login Perusahaan ---
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const loginButtonText = document.getElementById('loginButtonText');
    
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        loginButton.disabled = true;
        loginButtonText.textContent = 'Signing in...';
        
        const formData = new FormData(loginForm);
        
        try {
            const response = await fetch('{{ route("auth.login") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showMessage(successMessage, data.message || 'Login successful! Redirecting...', true);
                setTimeout(() => {
                    window.location.href = data.redirect_url || '{{ route("dashboard") }}';
                }, 1000);
            } else {
                showMessage(errorMessage, data.message || 'Login failed. Please check your credentials.', false);
            }
        } catch (error) {
            console.error('Login error:', error);
            showMessage(errorMessage, 'Login error. Please try again.', false);
        } finally {
            loginButton.disabled = false;
            loginButtonText.textContent = 'Sign In';
        }
    });

    // --- Fungsi Helper ---
    function showMessage(element, message, isSuccess = true) {
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');
        element.textContent = message;
        element.classList.remove('hidden');
        setTimeout(() => {
            element.classList.add('hidden');
        }, 5000);
    }
});
</script>
@endsection