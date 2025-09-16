@extends('layouts.account')

@section('title', 'Personal Info - My Account')

@push('styles')
<style>
    .personal-info-header {
        margin-bottom: 1.5rem;
    }
    
    .personal-info-title {
        font-size: 1.5rem;
        font-weight: 500;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .personal-info-description {
        color: #6b7280;
    }
    
    .profile-card {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .profile-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .profile-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .profile-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .profile-description {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .profile-avatar {
        width: 4rem;
        height: 4rem;
        border-radius: 50%;
        background-color: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-weight: 600;
    }
    
    .profile-body {
        padding: 1.5rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .linked-accounts-icon-container {
        width: 4rem;
        height: 4rem;
        background-color: #fff7ed;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .linked-accounts-icon {
        width: 2rem;
        height: 2rem;
        background-color: var(--swa-orange);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .save-btn-container {
        display: flex;
        justify-content: flex-end;
    }
    
    .success-message {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: none;
    }
    
    .error-message {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: none;
    }
</style>
@endpush

@section('account-content')
<div class="personal-info-header">
    <h1 class="personal-info-title">Personal Info</h1>
    <p class="personal-info-description">Edit or export your personal profile and manage linked accounts</p>
</div>

<!-- Success/Error Messages -->
<div id="successMessage" class="success-message"></div>
<div id="errorMessage" class="error-message"></div>

<!-- Profile Section -->
<div class="profile-card">
    <div class="profile-header">
        <div class="profile-header-content">
            <div>
                <h2 class="profile-title">Profile</h2>
                <p class="profile-description">Manage your personal profile</p>
            </div>
            <div class="profile-avatar">
                {{ $user['initials'] ?? '?' }}
            </div>
        </div>
    </div>
    <div class="profile-body">
        <form id="profileForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="{{ $user['username'] ?? 'john.doe' }}" style="background-color: #f9fafb;">
                </div>
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" 
                           placeholder="Enter your first name" value="{{ $user['given_name'] ?? 'John' }}">
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" 
                           placeholder="Enter your last name" value="{{ $user['family_name'] ?? 'Doe' }}">
                </div>
                <div class="form-group">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" name="country" class="form-control" 
                           placeholder="Enter your country" value="{{ $user['country'] ?? 'United States' }}">
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Enter your email" value="{{ $user['email'] }}" required>
                </div>
                <div class="form-group">
                    <label for="mobile" class="form-label">Mobile</label>
                    <input type="tel" id="mobile" name="mobile" class="form-control" 
                           placeholder="Enter your mobile" value="{{ $user['mobile'] ?? '+1 (555) 123-4567' }}">
                </div>
            </div>
            <div class="save-btn-container">
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <span id="saveBtnText">Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Linked Accounts Section -->
<div class="profile-card">
    <div class="profile-header">
        <div class="profile-header-content">
            <div>
                <h2 class="profile-title">Linked Accounts</h2>
                <p class="profile-description">Link/associate your other accounts, and access them seamlessly without re-login</p>
            </div>
            <div class="linked-accounts-icon-container">
                <div class="linked-accounts-icon">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="profile-body">
        <button type="button" class="btn btn-outline-orange">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="mr-2">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
            Add account
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const saveBtn = document.getElementById('saveBtn');
    const saveBtnText = document.getElementById('saveBtnText');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    function showMessage(element, message, isSuccess = true) {
        // Hide all messages first
        successMessage.style.display = 'none';
        errorMessage.style.display = 'none';
        
        // Show the appropriate message
        element.textContent = message;
        element.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            element.style.display = 'none';
        }, 5000);
    }

    profileForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        showLoading(saveBtn, true);
        saveBtnText.textContent = 'Saving...';
        
        const formData = new FormData(profileForm);
        const data = {
            first_name: formData.get('first_name'),
            last_name: formData.get('last_name'),
            email: formData.get('email'),
            mobile: formData.get('mobile'),
            country: formData.get('country'),
        };
        
        try {
            const response = await makeRequest('{{ route("account.profile.update") }}', {
                method: 'PUT',
                body: JSON.stringify(data)
            });
            
            if (response.success) {
                showMessage(successMessage, response.message || 'Profile updated successfully!', true);
                // Update the avatar initials if name changed
                const firstInitial = data.first_name ? data.first_name.charAt(0).toUpperCase() : '';
                const lastInitial = data.last_name ? data.last_name.charAt(0).toUpperCase() : '';
                document.querySelector('.profile-avatar').textContent = firstInitial + lastInitial;
            } else {
                showMessage(errorMessage, response.message || 'Failed to update profile. Please try again.', false);
            }
        } catch (error) {
            console.error('Profile update error:', error);
            showMessage(errorMessage, 'Error updating profile. Please try again.', false);
        } finally {
            showLoading(saveBtn, false);
            saveBtnText.textContent = 'Save Changes';
        }
    });
});
</script>
@endpush