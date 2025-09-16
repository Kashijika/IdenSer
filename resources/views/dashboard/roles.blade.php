@extends('layouts.dashboard')

@section('title', 'Roles & Permissions')

@push('styles')
<style>
    .roles-container {
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
    
    .permission-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .permission-tag {
        background: #e5e7eb;
        color: #374151;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
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
        max-width: 600px;
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
    
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background: white;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .permission-group {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
    }
    
    .permission-group-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .checkbox-group:last-child {
        margin-bottom: 0;
    }
    
    .checkbox {
        width: 1rem;
        height: 1rem;
        border: 2px solid #d1d5db;
        border-radius: 0.25rem;
        position: relative;
        cursor: pointer;
        background: white;
    }
    
    .checkbox:checked {
        background: #1e3a8a;
        border-color: #1e3a8a;
    }
    
    .checkbox:checked::after {
        content: '✓';
        position: absolute;
        top: -2px;
        left: 1px;
        color: white;
        font-size: 0.75rem;
        font-weight: bold;
    }
    
    .checkbox-label {
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .user-count {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .user-avatars {
        display: flex;
        gap: -0.25rem;
        margin-left: 0.5rem;
    }
    
    .user-avatar {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        background: #e5e7eb;
        color: #374151;
        font-size: 0.625rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        margin-left: -0.25rem;
    }
    
    .user-avatar:first-child {
        margin-left: 0;
    }
</style>
@endpush

@section('content')
<div class="roles-container">
    <div class="page-header">
        <h1 class="page-title">Roles & Permissions</h1>
        <p class="page-description">Manage system roles and their permissions</p>
    </div>

    <div class="page-actions">
        <div></div>
        @if($user['role_name'] === 'admin')
        <button class="btn btn-primary" id="addRoleBtn">
            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
            Add Role
        </button>
        @endif
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Permissions</th>
                        <th>Assigned Users</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span class="role-badge {{ $role->name }}">{{ $role->display_name }}</span>
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 200px;">
                                {{ $role->description ?? 'No description provided' }}
                            </div>
                        </td>
                        <td>
                            <div class="permission-tags">
                                @forelse($role->permissions as $permission)
                                <span class="permission-tag">{{ $permission->display_name }}</span>
                                @empty
                                <span style="color: #6b7280; font-style: italic;">No permissions</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <span class="user-count">
                                    <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    {{ $role->users->count() }}
                                </span>
                                @if($role->users->count() > 0)
                                <div class="user-avatars">
                                    @foreach($role->users->take(3) as $user)
                                    <div class="user-avatar" title="{{ $user->first_name }} {{ $user->last_name }}">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    @endforeach
                                    @if($role->users->count() > 3)
                                    <div class="user-avatar" title="{{ $role->users->count() - 3 }} more users">
                                        +{{ $role->users->count() - 3 }}
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>{{ $role->created_at->format('M j, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($user['role_name'] === 'admin')
                                <button class="btn btn-sm btn-secondary edit-role-btn" 
                                        data-role-id="{{ $role->id }}"
                                        data-role-name="{{ $role->name }}"
                                        data-role-display-name="{{ $role->display_name }}"
                                        data-role-description="{{ $role->description }}"
                                        data-role-permissions="{{ $role->permissions->pluck('id')->implode(',') }}">
                                    <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Edit
                                </button>
                                @if(!in_array($role->name, ['admin', 'hr', 'employee']))
                                <button class="btn btn-sm btn-danger delete-role-btn" 
                                        data-role-id="{{ $role->id }}" 
                                        data-role-name="{{ $role->display_name }}"
                                        data-user-count="{{ $role->users->count() }}">
                                    <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path d="M4 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM6 5a1 1 0 012 2v6a1 1 0 01-2 0V7a1 1 0 00-1-1zm6 0a1 1 0 012 2v6a1 1 0 01-2 0V7a1 1 0 00-1-1z"/>
                                    </svg>
                                    Delete
                                </button>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination if needed -->
        @if(method_exists($roles, 'links'))
        <div style="margin-top: 2rem;">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add/Edit Role Modal -->
<div class="modal" id="roleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add New Role</h3>
            <button class="close-btn" id="closeModal">&times;</button>
        </div>
        
        <form id="roleForm">
            <input type="hidden" id="roleId" name="role_id">
            
            <div class="form-group">
                <label class="form-label" for="roleName">Role Name *</label>
                <input type="text" id="roleName" name="name" class="form-input" 
                       placeholder="e.g., manager, analyst" required>
                <small style="color: #6b7280; font-size: 0.75rem;">Use lowercase letters, numbers, and underscores only</small>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="roleDisplayName">Display Name *</label>
                <input type="text" id="roleDisplayName" name="display_name" class="form-input" 
                       placeholder="e.g., Manager, Data Analyst" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="roleDescription">Description</label>
                <input type="text" id="roleDescription" name="description" class="form-input" 
                       placeholder="Brief description of the role">
            </div>
            
            <div class="form-group">
                <label class="form-label">Permissions</label>
                <div class="permissions-grid">
                    <div class="permission-group">
                        <div class="permission-group-title">User Management</div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_manage_users" name="permissions[]" 
                                   value="manage_users" class="checkbox">
                            <label for="perm_manage_users" class="checkbox-label">Manage Users</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_change_user_roles" name="permissions[]" 
                                   value="change_user_roles" class="checkbox">
                            <label for="perm_change_user_roles" class="checkbox-label">Change User Roles</label>
                        </div>
                    </div>
                    
                    <div class="permission-group">
                        <div class="permission-group-title">Role Management</div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_manage_roles" name="permissions[]" 
                                   value="manage_roles" class="checkbox">
                            <label for="perm_manage_roles" class="checkbox-label">Manage Roles</label>
                        </div>
                    </div>
                    
                    <div class="permission-group">
                        <div class="permission-group-title">Trading Data</div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_access_trading_data" name="permissions[]" 
                                   value="access_trading_data" class="checkbox">
                            <label for="perm_access_trading_data" class="checkbox-label">Access Trading Data</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_export_trading_data" name="permissions[]" 
                                   value="export_trading_data" class="checkbox">
                            <label for="perm_export_trading_data" class="checkbox-label">Export Trading Data</label>
                        </div>
                    </div>
                    
                    <div class="permission-group">
                        <div class="permission-group-title">Security</div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_change_security_policy" name="permissions[]" 
                                   value="change_security_policy" class="checkbox">
                            <label for="perm_change_security_policy" class="checkbox-label">Change Security Policy</label>
                        </div>
                    </div>
                    
                    <div class="permission-group">
                        <div class="permission-group-title">Audit & Monitoring</div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="perm_view_audit_logs" name="permissions[]" 
                                   value="view_audit_logs" class="checkbox">
                            <label for="perm_view_audit_logs" class="checkbox-label">View Audit Logs</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Save Role</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleModal = document.getElementById('roleModal');
    const roleForm = document.getElementById('roleForm');
    const addRoleBtn = document.getElementById('addRoleBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalTitle = document.getElementById('modalTitle');
    
    // Modal functionality
    function openModal(isEdit = false) {
        roleModal.classList.add('active');
        if (isEdit) {
            modalTitle.textContent = 'Edit Role';
        } else {
            modalTitle.textContent = 'Add New Role';
            roleForm.reset();
        }
    }
    
    function closeModalFn() {
        roleModal.classList.remove('active');
        roleForm.reset();
    }
    
    if (addRoleBtn) {
        addRoleBtn.addEventListener('click', () => openModal(false));
    }
    
    closeModal.addEventListener('click', closeModalFn);
    cancelBtn.addEventListener('click', closeModalFn);
    
    // Auto-generate role display name from role name
    document.getElementById('roleName').addEventListener('input', function() {
        if (!document.getElementById('roleDisplayName').value) {
            const displayName = this.value
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
            document.getElementById('roleDisplayName').value = displayName;
        }
    });
    
    // Edit role buttons
    document.querySelectorAll('.edit-role-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const roleId = this.getAttribute('data-role-id');
            const roleName = this.getAttribute('data-role-name');
            const roleDisplayName = this.getAttribute('data-role-display-name');
            const roleDescription = this.getAttribute('data-role-description');
            const rolePermissions = this.getAttribute('data-role-permissions').split(',');
            
            // Populate form
            document.getElementById('roleId').value = roleId;
            document.getElementById('roleName').value = roleName;
            document.getElementById('roleDisplayName').value = roleDisplayName;
            document.getElementById('roleDescription').value = roleDescription;
            
            // Clear all checkboxes first
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check the appropriate permissions
            rolePermissions.forEach(permissionId => {
                const checkbox = document.querySelector(`input[name="permissions[]"][value="${permissionId}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
            
            openModal(true);
        });
    });
    
    // Delete role buttons
    document.querySelectorAll('.delete-role-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const roleId = this.getAttribute('data-role-id');
            const roleName = this.getAttribute('data-role-name');
            const userCount = parseInt(this.getAttribute('data-user-count'));
            
            let message = `Are you sure you want to delete the role "${roleName}"?`;
            if (userCount > 0) {
                message += `\n\nWarning: This role is currently assigned to ${userCount} user(s). They will lose their current permissions.`;
            }
            message += '\n\nThis action cannot be undone.';
            
            if (confirm(message)) {
                makeRequest(`/dashboard/roles/${roleId}`, {
                    method: 'DELETE'
                }).then(response => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Error deleting role');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting role');
                });
            }
        });
    });
    
    // Form submission
    roleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const roleId = formData.get('role_id');
        const isEdit = roleId && roleId !== '';
        
        const url = isEdit ? `/dashboard/roles/${roleId}` : '/dashboard/roles';
        const method = isEdit ? 'PUT' : 'POST';
        
        // Convert FormData to JSON for API call
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key === 'permissions[]') {
                if (!data.permissions) {
                    data.permissions = [];
                }
                data.permissions.push(value);
            } else if (key !== 'role_id' || value !== '') {
                data[key] = value;
            }
        }
        
        // Ensure permissions is always an array
        if (!data.permissions) {
            data.permissions = [];
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
                alert(response.message || 'Error saving role');
            }
        }).catch(error => {
            showLoading(document.getElementById('saveBtn'), false);
            console.error('Error:', error);
            alert('Error saving role');
        });
    });
    
    // Close modal when clicking outside
    roleModal.addEventListener('click', function(e) {
        if (e.target === roleModal) {
            closeModalFn();
        }
    });
    
    // Custom checkbox styling
    document.querySelectorAll('.checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                this.classList.add('checked');
            } else {
                this.classList.remove('checked');
            }
        });
    });
});
</script>
@endpush