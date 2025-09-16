@extends('layouts.dashboard')

@section('title', 'Users Management')

@push('styles')
<style>
    .users-container {
        max-width: none;
    }
    
    .page-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
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
    
    .btn-danger {
        background: #dc2626;
        color: white;
    }
    
    .btn-danger:hover {
        background: #b91c1c;
        color: white;
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .tabs {
        margin-bottom: 2rem;
    }
    
    .tab-list {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 2rem;
    }
    
    .tab-button {
        padding: 1rem 1.5rem;
        border: none;
        background: none;
        color: #6b7280;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .tab-button:hover {
        color: #374151;
        background: #f9fafb;
    }
    
    .tab-button.active {
        color: #1e3a8a;
        border-bottom-color: #1e3a8a;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .table-container {
        overflow-x: auto;
        margin: -1.5rem;
        padding: 1.5rem;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th,
    .table td {
        padding: 1rem;
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
    }
    
    .table tr:hover {
        background: #f9fafb;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-badge.active {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-badge.inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .role-badge.admin {
        background: #fef3c7;
        color: #92400e;
    }
    
    .role-badge.hr {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .role-badge.employee {
        background: #f3f4f6;
        color: #374151;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .close-btn:hover {
        color: #374151;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
    }
    
    .form-input,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background: white;
    }
    
    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
    
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 2rem;
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
    
    .search-filter {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: end;
    }
    
    .search-group {
        flex: 1;
    }
    
    .filter-group {
        min-width: 150px;
    }
</style>
@endpush

@section('content')
<div class="users-container">
    <div class="page-header">
        <h1 class="page-title">Users Management</h1>
        <p class="page-description">Manage user accounts, roles, and permissions</p>
    </div>

    <div class="page-actions">
        <div class="tabs">
            <div class="tab-list">
                <button class="tab-button active" data-tab="users">User List</button>
                @if(in_array($user['role_name'] ?? '', ['admin', 'hr']))
                <button class="tab-button" data-tab="role-requests">Role Change Requests</button>
                @endif
            </div>
        </div>
        
        @if(in_array($user['role_name'] ?? '', ['admin', 'hr']))
        <button class="btn btn-primary" id="addUserBtn">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
            Add User
        </button>
        @endif
    </div>

    <!-- Users Tab -->
    <div class="tab-content active" id="users-tab">
        <div class="card">
            <!-- Search and Filter -->
            <div class="search-filter">
                <div class="search-group">
                    <label class="form-label" for="search">Search Users</label>
                    <input type="text" id="search" class="form-input" placeholder="Search by name or email...">
                </div>
                <div class="filter-group">
                    <label class="form-label" for="roleFilter">Filter by Role</label>
                    <select id="roleFilter" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label" for="statusFilter">Filter by Status</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @foreach($users as $user)
                        <tr data-role="{{ $user->role->name ?? '' }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                            <td>
                                <div>
                                    <div style="font-weight: 500;">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    <div style="color: #6b7280; font-size: 0.875rem;">{{ $user->username }}</div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role)
                                <span class="role-badge {{ $user->role->name }}">{{ $user->role->display_name }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    @if(in_array($user['role_name'] ?? '', ['admin', 'hr']))
                                    <button class="btn btn-sm btn-secondary edit-user-btn" 
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role_id ?? '' }}"
                                            data-user-status="{{ $user->is_active ? '1' : '0' }}">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-user-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path d="M4 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM6 5a1 1 0 012 2v6a1 1 0 01-2 0V7a1 1 0 00-1-1zm6 0a1 1 0 012 2v6a1 1 0 01-2 0V7a1 1 0 00-1-1z"/>
                                        </svg>
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Role Change Requests Tab -->
    @if(in_array($user['role_name'] ?? '', ['admin', 'hr']))
    <div class="tab-content" id="role-requests-tab">
        <div class="card">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.125rem; font-weight: 600;">Pending Role Change Requests</h3>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Requester</th>
                            <th>Current Role</th>
                            <th>Requested Role</th>
                            <th>Reason</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roleChangeRequests as $request)
                        <tr>
                            <td>
                                <div>
                                    <div style="font-weight: 500;">{{ $request->user->first_name }} {{ $request->user->last_name }}</div>
                                    <div style="color: #6b7280; font-size: 0.875rem;">{{ $request->user->email }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge {{ $request->user->role->name ?? 'employee' }}">
                                    {{ $request->user->role->display_name ?? 'Employee' }}
                                </span>
                            </td>
                            <td>
                                <span class="role-badge {{ $request->requestedRole->name }}">{{ $request->requestedRole->display_name }}</span>
                            </td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $request->reason }}
                                </div>
                            </td>
                            <td>{{ $request->created_at->format('M j, Y H:i') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-success approve-request-btn" 
                                            data-request-id="{{ $request->id }}"
                                            data-user-name="{{ $request->user->first_name }} {{ $request->user->last_name }}">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-request-btn" 
                                            data-request-id="{{ $request->id }}"
                                            data-user-name="{{ $request->user->first_name }} {{ $request->user->last_name }}">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                        </svg>
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6b7280; padding: 2rem;">
                                No pending role change requests
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Add/Edit User Modal -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add New User</h3>
            <button class="close-btn" id="closeModal">&times;</button>
        </div>
        
        <form id="userForm">
            <input type="hidden" id="userId" name="user_id">
            
            <div class="form-group">
                <label class="form-label" for="firstName">First Name *</label>
                <input type="text" id="firstName" name="first_name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="last_name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-input" required>
            </div>
            
            <div class="form-group" id="passwordGroup">
                <label class="form-label" for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="role">Role *</label>
                <select id="role" name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="is_active" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Save User</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userModal = document.getElementById('userModal');
    const userForm = document.getElementById('userForm');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalTitle = document.getElementById('modalTitle');
    const passwordGroup = document.getElementById('passwordGroup');
    const passwordField = document.getElementById('password');
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId + '-tab').classList.add('active');
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('search');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#usersTableBody tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleFilterValue = roleFilter.value;
        const statusFilterValue = statusFilter.value;
        
        tableRows.forEach(row => {
            const name = row.querySelector('td:first-child').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const role = row.getAttribute('data-role');
            const status = row.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleFilterValue || role === roleFilterValue;
            const matchesStatus = !statusFilterValue || status === statusFilterValue;
            
            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    // Modal functionality
    function openModal(isEdit = false) {
        userModal.classList.add('active');
        if (isEdit) {
            modalTitle.textContent = 'Edit User';
            passwordGroup.style.display = 'none';
            passwordField.required = false;
        } else {
            modalTitle.textContent = 'Add New User';
            passwordGroup.style.display = 'block';
            passwordField.required = true;
            userForm.reset();
        }
    }
    
    function closeModalFn() {
        userModal.classList.remove('active');
        userForm.reset();
    }
    
    if (addUserBtn) {
        addUserBtn.addEventListener('click', () => openModal(false));
    }
    
    closeModal.addEventListener('click', closeModalFn);
    cancelBtn.addEventListener('click', closeModalFn);
    
    // Edit user buttons
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const userEmail = this.getAttribute('data-user-email');
            const userRole = this.getAttribute('data-user-role');
            const userStatus = this.getAttribute('data-user-status');
            
            // Populate form
            document.getElementById('userId').value = userId;
            const nameParts = userName.split(' ');
            document.getElementById('firstName').value = nameParts[0] || '';
            document.getElementById('lastName').value = nameParts.slice(1).join(' ') || '';
            document.getElementById('email').value = userEmail;
            document.getElementById('role').value = userRole;
            document.getElementById('status').value = userStatus;
            
            openModal(true);
        });
    });
    
    // Delete user buttons
    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                makeRequest(`/dashboard/users/${userId}`, {
                    method: 'DELETE'
                }).then(response => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Error deleting user');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user');
                });
            }
        });
    });
    
    // Approve role request buttons
    document.querySelectorAll('.approve-request-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const userName = this.getAttribute('data-user-name');
            
            if (confirm(`Approve role change request for "${userName}"?`)) {
                makeRequest(`/dashboard/role-requests/${requestId}/approve`, {
                    method: 'POST'
                }).then(response => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Error approving request');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error approving request');
                });
            }
        });
    });
    
    // Reject role request buttons
    document.querySelectorAll('.reject-request-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const userName = this.getAttribute('data-user-name');
            
            if (confirm(`Reject role change request for "${userName}"?`)) {
                makeRequest(`/dashboard/role-requests/${requestId}/reject`, {
                    method: 'POST'
                }).then(response => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Error rejecting request');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error rejecting request');
                });
            }
        });
    });
    
    // Form submission
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const userId = formData.get('user_id');
        const isEdit = userId && userId !== '';
        
        const url = isEdit ? `/dashboard/users/${userId}` : '/dashboard/users';
        const method = isEdit ? 'PUT' : 'POST';
        
        // Convert FormData to JSON for API call
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key !== 'user_id' || value !== '') {
                data[key] = value;
            }
        }
        
        showLoading(document.getElementById('saveBtn'), true);
        
        makeRequest(url, {
            method: method,
            body: JSON.stringify(data)
        }).then(response => {
            showLoading(document.getElementById('saveBtn'), false);
            
            if (response.success) {
                closeModalFn();
                location.reload();
            } else {
                alert(response.message || 'Error saving user');
            }
        }).catch(error => {
            showLoading(document.getElementById('saveBtn'), false);
            console.error('Error:', error);
            alert('Error saving user');
        });
    });
    
    // Close modal when clicking outside
    userModal.addEventListener('click', function(e) {
        if (e.target === userModal) {
            closeModalFn();
        }
    });
});
</script>
@endpush