@extends('layouts.dashboard')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: <!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="welcome-title">Welcome back, 
        @if(isset($user['given_name']))
            {{ $user['given_name'] }}
        @elseif(isset($user['first_name']))
            {{ $user['first_name'] }}
        @elseif(isset($user['name']))
            {{ $user['name'] }}
        @else
            User
        @endif
    !</h1>
    <p class="welcome-subtitle">Here's what's happening with your SWA Media account today.</p>
    
    <!-- Role Badge -->
    <div style="margin-top: 1rem;">
        <span class="role-badge {{ $user['role_name'] ?? 'employee' }}">
            {{ ucfirst($user['role_name'] ?? 'Employee') }}</span> }
    
    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: all 0.2s ease;
    }
    
    .stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .stat-title {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .stat-change {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    
    .stat-change.positive {
        color: #059669;
    }
    
    .stat-change.negative {
        color: #dc2626;
    }
    
    .chart-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    
    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
    }
    
    .mini-chart {
        height: 200px;
        display: flex;
        align-items: end;
        gap: 0.25rem;
        padding: 1rem 0;
    }
    
    .chart-bar {
        background: linear-gradient(to top, #1e3a8a, #3b82f6);
        border-radius: 0.125rem;
        flex: 1;
        min-height: 10px;
        transition: all 0.2s ease;
    }
    
    .chart-bar:hover {
        background: linear-gradient(to top, #1e40af, #60a5fa);
    }
    
    .activity-feed {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
    
    .activity-item {
        display: flex;
        align-items: start;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 500;
        color: #111827;
        font-size: 0.875rem;
    }
    
    .activity-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0.25rem 0;
    }
    
    .activity-time {
        color: #9ca3af;
        font-size: 0.75rem;
    }
    
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .role-badge.admin {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .role-badge.hr {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .role-badge.employee {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .welcome-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 0.75rem;
        color: white;
    }
    
    .welcome-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .welcome-subtitle {
        opacity: 0.9;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .quick-action {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.5rem;
        padding: 1rem;
        text-decoration: none;
        color: white;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .quick-action:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
        color: white;
    }
    
    .quick-action-icon {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>
@endpush

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="welcome-title">Welcome back, 
        @if(isset($user['given_name']))
            {{ $user['given_name'] }}
        @elseif(isset($user['first_name']))
            {{ $user['first_name'] }}
        @elseif(isset($user['name']))
            {{ $user['name'] }}
        @else
            User
        @endif
    !</h1>
    <p class="welcome-subtitle">Here's what's happening with your SWA Media account today.</p>
    
    <!-- Role Badge -->
    <div style="margin-top: 1rem;">
        <span class="role-badge {{ $user['role_name'] ?? 'employee' }}">
            {{ ucfirst($user['role_name'] ?? 'Employee') }}
        </span>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        @if(in_array($user['role_name'] ?? '', ['Admin', 'Human Resources']))
        <a href="{{ route('dashboard.users') }}" class="quick-action">
            <svg class="quick-action-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <span>Manage Users</span>
        </a>
        @endif
        
        @if(($user['role_name'] ?? '') === 'Admin')
        <a href="{{ route('dashboard.roles') }}" class="quick-action">
            <svg class="quick-action-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Manage Roles</span>
        </a>
        @endif
        
        <a href="{{ route('dashboard.trading-data') }}" class="quick-action">
            <svg class="quick-action-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1-1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
            </svg>
            <span>View Trading Data</span>
        </a>
        
        <a href="{{ route('account') }}" class="quick-action">
            <svg class="quick-action-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 8a2 2 0 110-4 2 2 0 010 4zM10 18l3-3h-6l3 3z"/>
                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
            </svg>
            <span>My Profile</span>
        </a>
    </div>
</div>

<!-- Statistics Grid -->
<div class="stats-grid">
    <!-- Total Users -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Total Users</h3>
            <div class="stat-icon" style="background-color: #dbeafe;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #1e40af;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
        <div class="stat-change positive">
            <svg style="width: 1rem; height: 1rem; margin-right: 0.25rem;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"/>
            </svg>
            <span>Active accounts</span>
        </div>
    </div>

    <!-- Total Roles -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Active Roles</h3>
            <div class="stat-icon" style="background-color: #f3e8ff;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #7c3aed;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['total_roles'] }}</div>
        <div class="stat-change">
            <span>System roles configured</span>
        </div>
    </div>

    <!-- Recent Logins -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Recent Activity</h3>
            <div class="stat-icon" style="background-color: #ecfdf5;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['recent_logins'] }}</div>
        <div class="stat-change">
            <span>Logins this week</span>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Pending Requests</h3>
            <div class="stat-icon" style="background-color: #fef3c7;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $stats['pending_role_requests'] }}</div>
        <div class="stat-change">
            <span>Role change requests</span>
        </div>
    </div>
</div>

<!-- Trading Data Mini Chart -->
<div class="chart-container">
    <div class="chart-header">
        <h3 class="chart-title">Trading Data Overview</h3>
        <a href="{{ route('dashboard.trading-data') }}" style="color: #1e3a8a; text-decoration: none; font-size: 0.875rem;">
            View Details →
        </a>
    </div>
    <div class="mini-chart">
        @if(isset($tradingData) && $tradingData->count() > 0)
            @foreach($tradingData->take(8) as $index => $data)
            <div class="chart-bar" 
                 style="height: {{ min(100, ($data->volume / 1000000) * 20) }}%;" 
                 title="{{ $data->symbol }}: ${{ number_format($data->price, 2) }}">
            </div>
            @endforeach
        @else
            <div style="display: flex; align-items: center; justify-content: center; height: 100px; color: #6b7280; font-size: 0.875rem;">
                <svg style="width: 2rem; height: 2rem; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                No trading data available
            </div>
        @endif
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 1rem; color: #6b7280; font-size: 0.75rem;">
        @if(isset($tradingData) && $tradingData->count() > 0)
            @foreach($tradingData->take(8) as $data)
            <span>{{ $data->symbol }}</span>
            @endforeach
        @endif
    </div>
</div>

<!-- Recent Activity Feed -->
<div class="activity-feed">
    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1.5rem;">Recent Activity</h3>
    
    @forelse($recentActivity as $activity)
    <div class="activity-item">
        <div class="activity-icon">
            @switch($activity->action)
                @case('user_login')
                    <svg style="width: 1rem; height: 1rem; color: #059669;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3zM7 11a1 1 0 000 2h6a1 1 0 100-2H7z"/>
                    </svg>
                    @break
                @case('user_logout')
                    <svg style="width: 1rem; height: 1rem; color: #dc2626;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"/>
                    </svg>
                    @break
                @default
                    <svg style="width: 1rem; height: 1rem; color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                    </svg>
            @endswitch
        </div>
        <div class="activity-content">
            <div class="activity-title">{{ $activity->description }}</div>
            <div class="activity-description">
                @if($activity->user_name)
                    by {{ $activity->user_name }}
                @elseif($activity->wso2_user_id)
                    by User {{ $activity->wso2_user_id }}
                @else
                    System action
                @endif
            </div>
            <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @empty
    <div style="text-align: center; color: #6b7280; padding: 2rem;">
        <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; opacity: 0.5;" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8.5 8a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0zm-3 3a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0z"/>
        </svg>
        <p>No recent activity to display</p>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
// Add some interactive functionality to the dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Animate chart bars on hover
    const chartBars = document.querySelectorAll('.chart-bar');
    chartBars.forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            this.style.transform = 'scaleY(1.1)';
        });
        
        bar.addEventListener('mouseleave', function() {
            this.style.transform = 'scaleY(1)';
        });
    });
    
    // Auto-refresh stats every 5 minutes (optional)
    setInterval(function() {
        // Could implement AJAX refresh here
        console.log('Dashboard stats could be refreshed here');
    }, 300000);
});
</script>
@endpush