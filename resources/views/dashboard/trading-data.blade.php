@extends('layouts.dashboard')

@section('title', 'Trading Data')

@push('styles')
<style>
    .trading-container {
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
    
    .btn-success {
        background: #059669;
        color: white;
    }
    
    .btn-success:hover {
        background: #047857;
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
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
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
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
    }
    
    .chart-controls {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .time-filter {
        display: flex;
        background: #f3f4f6;
        border-radius: 0.5rem;
        padding: 0.25rem;
    }
    
    .time-filter button {
        padding: 0.5rem 1rem;
        border: none;
        background: none;
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .time-filter button:hover {
        color: #374151;
    }
    
    .time-filter button.active {
        background: white;
        color: #1e3a8a;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .line-chart {
        height: 300px;
        position: relative;
        border: 1px solid #f3f4f6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background: linear-gradient(to bottom, transparent 0%, transparent 50%, rgba(30, 58, 138, 0.05) 100%);
    }
    
    .chart-line {
        stroke: #1e3a8a;
        stroke-width: 2;
        fill: none;
    }
    
    .chart-area {
        fill: url(#gradient);
        opacity: 0.3;
    }
    
    .bar-chart {
        height: 300px;
        display: flex;
        align-items: end;
        gap: 0.5rem;
        padding: 1rem;
        border: 1px solid #f3f4f6;
        border-radius: 0.5rem;
        background: #f9fafb;
    }
    
    .chart-bar {
        background: linear-gradient(to top, #1e3a8a, #3b82f6);
        border-radius: 0.25rem;
        flex: 1;
        min-height: 10px;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
    }
    
    .chart-bar:hover {
        background: linear-gradient(to top, #1e40af, #60a5fa);
        transform: translateY(-2px);
    }
    
    .chart-bar::after {
        content: attr(data-value);
        position: absolute;
        top: -1.5rem;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.75rem;
        color: #6b7280;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .chart-bar:hover::after {
        opacity: 1;
    }
    
    .chart-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        padding: 0 1rem;
        color: #6b7280;
        font-size: 0.75rem;
    }
    
    .access-notice {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .access-notice.employee {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #92400e;
    }
    
    .access-notice.hr {
        background: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
    }
    
    .access-notice.admin {
        background: #d1fae5;
        border-color: #10b981;
        color: #065f46;
    }
    
    .data-table {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .table-header {
        background: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .table-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .table-description {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .table-container {
        overflow-x: auto;
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
    }
    
    .table tr:hover {
        background: #f9fafb;
    }
    
    .symbol-cell {
        font-weight: 600;
        color: #111827;
    }
    
    .price-cell {
        font-weight: 600;
    }
    
    .price-positive {
        color: #059669;
    }
    
    .price-negative {
        color: #dc2626;
    }
    
    .volume-bar {
        background: #e5e7eb;
        height: 0.5rem;
        border-radius: 0.25rem;
        overflow: hidden;
        position: relative;
    }
    
    .volume-fill {
        background: linear-gradient(to right, #1e3a8a, #3b82f6);
        height: 100%;
        border-radius: 0.25rem;
        transition: width 0.3s ease;
    }
    
    .export-buttons {
        display: flex;
        gap: 0.5rem;
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
    
    /* Responsive design */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .chart-controls {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .export-buttons {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="trading-container">
    <div class="page-header">
        <h1 class="page-title">Trading Data</h1>
        <p class="page-description">View trading analytics and market data</p>
    </div>

    <!-- Access Level Notice -->
    <div class="access-notice {{ $user['role_name'] ?? 'employee' }}">
        <svg style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
            @switch($user['role_name'] ?? 'employee')
                @case('admin')
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @break
                @case('hr')
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @break
                @default
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            @endswitch
        </svg>
        <div>
            @switch($user['role_name'] ?? 'employee')
                @case('admin')
                    <strong>Full Access:</strong> You have complete access to all trading data, analytics, and export capabilities.
                    @break
                @case('hr')
                    <strong>Extended Access:</strong> You can view detailed trading data and export reports for analysis.
                    @break
                @default
                    <strong>Limited Access:</strong> You have access to anonymized trading data for general market insights only.
            @endswitch
        </div>
    </div>

    <!-- Trading Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Total Volume</h3>
                <div class="stat-icon" style="background-color: #dbeafe;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: #1e40af;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">${{ number_format($stats['total_volume'] / 1000000, 1) }}M</div>
            <div class="stat-change positive">
                <svg style="width: 1rem; height: 1rem; margin-right: 0.25rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"/>
                </svg>
                <span>+12.3% from last week</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Active Symbols</h3>
                <div class="stat-icon" style="background-color: #f3e8ff;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: #7c3aed;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['active_symbols'] }}</div>
            <div class="stat-change">
                <span>Currently trading</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Average Price</h3>
                <div class="stat-icon" style="background-color: #ecfdf5;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: #059669;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">${{ number_format($stats['average_price'], 2) }}</div>
            <div class="stat-change negative">
                <svg style="width: 1rem; height: 1rem; margin-right: 0.25rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"/>
                </svg>
                <span>-2.1% from yesterday</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Top Performer</h3>
                <div class="stat-icon" style="background-color: #fef3c7;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: #d97706;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['top_performer'] }}</div>
            <div class="stat-change positive">
                <span>+{{ $stats['top_performer_change'] }}% today</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">Trading Trends</h3>
            <div class="chart-controls">
                @if(in_array($user['role_name'] ?? '', ['admin', 'hr']))
                <div class="export-buttons">
                    <button class="btn btn-sm btn-success" id="exportCsv">
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3.293 12.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 8.414V15a1 1 0 11-2 0V8.414L4.707 12.707a1 1 0 01-1.414 0z"/>
                        </svg>
                        Export CSV
                    </button>
                    <button class="btn btn-sm btn-success" id="exportPdf">
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                        </svg>
                        Export PDF
                    </button>
                    <button class="btn btn-sm btn-success" id="exportExcel">
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8.5 8a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0zm-3 3a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 11-2 0 1 1 0 012 0z"/>
                        </svg>
                        Export Excel
                    </button>
                </div>
                @endif
                
                <div class="time-filter">
                    <button class="active" data-period="1d">1D</button>
                    <button data-period="1w">1W</button>
                    <button data-period="1m">1M</button>
                    <button data-period="3m">3M</button>
                    <button data-period="1y">1Y</button>
                </div>
            </div>
        </div>

        <!-- Line Chart -->
        <div style="margin-bottom: 2rem;">
            <h4 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">Price Trends</h4>
            <div class="line-chart">
                <svg width="100%" height="100%" viewBox="0 0 800 250">
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#1e3a8a;stop-opacity:0.3" />
                            <stop offset="100%" style="stop-color:#1e3a8a;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                    <!-- Sample trend line -->
                    <path class="chart-line" d="M50,180 Q150,120 250,140 T450,100 T650,120 T750,90" stroke="#1e3a8a" stroke-width="3" fill="none"/>
                    <path class="chart-area" d="M50,180 Q150,120 250,140 T450,100 T650,120 T750,90 L750,220 L50,220 Z"/>
                    
                    <!-- Data points -->
                    @foreach($chartData['price_points'] as $index => $point)
                    <circle cx="{{ 50 + ($index * 100) }}" cy="{{ 250 - ($point['value'] * 2) }}" r="4" 
                            fill="#1e3a8a" stroke="white" stroke-width="2"
                            title="Price: ${{ number_format($point['value'], 2) }}">
                    </circle>
                    @endforeach
                </svg>
            </div>
        </div>

        <!-- Bar Chart -->
        <div>
            <h4 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">Trading Volume</h4>
            <div class="bar-chart">
                @foreach($chartData['volume_data'] as $data)
                <div class="chart-bar" 
                     style="height: {{ min(100, ($data['volume'] / 5000000) * 100) }}%;" 
                     data-value="${{ number_format($data['volume'] / 1000000, 1) }}M"
                     title="{{ $data['symbol'] }}: ${{ number_format($data['volume'] / 1000000, 1) }}M volume">
                </div>
                @endforeach
            </div>
            <div class="chart-labels">
                @foreach($chartData['volume_data'] as $data)
                <span>{{ $data['symbol'] }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Trading Data Table -->
    <div class="data-table">
        <div class="table-header">
            <h3 class="table-title">Live Trading Data</h3>
            <p class="table-description">
                @if($user['role_name'] === 'employee')
                    Showing anonymized trading data for market analysis
                @else
                    Real-time trading data with full details
                @endif
            </p>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Symbol</th>
                        @if($user['role_name'] !== 'employee')
                        <th>Trader ID</th>
                        @endif
                        <th>Price</th>
                        <th>Change</th>
                        <th>Volume</th>
                        <th>Volume Progress</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tradingData as $data)
                    <tr>
                        <td class="symbol-cell">{{ $data->symbol }}</td>
                        @if($user['role_name'] !== 'employee')
                        <td>
                            <code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                {{ $data->trader_id ?? 'TR-' . str_pad($data->id, 4, '0', STR_PAD_LEFT) }}
                            </code>
                        </td>
                        @endif
                        <td class="price-cell">${{ number_format($data->price, 2) }}</td>
                        <td class="price-cell {{ $data->change >= 0 ? 'price-positive' : 'price-negative' }}">
                            {{ $data->change >= 0 ? '+' : '' }}{{ number_format($data->change, 2) }}%
                        </td>
                        <td>${{ number_format($data->volume / 1000000, 2) }}M</td>
                        <td>
                            <div class="volume-bar">
                                <div class="volume-fill" style="width: {{ min(100, ($data->volume / 10000000) * 100) }}%;"></div>
                            </div>
                        </td>
                        <td>
                            <div style="color: #6b7280; font-size: 0.875rem;">
                                {{ $data->updated_at->format('H:i:s') }}
                            </div>
                            <div style="color: #9ca3af; font-size: 0.75rem;">
                                {{ $data->updated_at->format('M j') }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $tradingData->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Time filter functionality
    const timeFilters = document.querySelectorAll('.time-filter button');
    
    timeFilters.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            timeFilters.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const period = this.getAttribute('data-period');
            loadChartData(period);
        });
    });
    
    // Load chart data based on selected period
    function loadChartData(period) {
        // Show loading state
        const chartBars = document.querySelectorAll('.chart-bar');
        chartBars.forEach(bar => {
            bar.style.opacity = '0.5';
        });
        
        // Simulate API call
        setTimeout(() => {
            // Restore chart
            chartBars.forEach(bar => {
                bar.style.opacity = '1';
                // Randomize heights for demo
                const randomHeight = Math.random() * 80 + 20;
                bar.style.height = randomHeight + '%';
            });
        }, 500);
    }
    
    // Export functionality
    const exportButtons = {
        csv: document.getElementById('exportCsv'),
        pdf: document.getElementById('exportPdf'),
        excel: document.getElementById('exportExcel')
    };
    
    Object.keys(exportButtons).forEach(format => {
        const button = exportButtons[format];
        if (button) {
            button.addEventListener('click', function() {
                exportData(format);
            });
        }
    });
    
    function exportData(format) {
        const button = exportButtons[format];
        showLoading(button, true);
        
        makeRequest(`/dashboard/trading-data/export/${format}`, {
            method: 'POST'
        }).then(response => {
            showLoading(button, false);
            
            if (response.success) {
                // Create download link
                const link = document.createElement('a');
                link.href = response.download_url;
                link.download = response.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert(response.message || `Error exporting ${format.toUpperCase()}`);
            }
        }).catch(error => {
            showLoading(button, false);
            console.error('Export error:', error);
            alert(`Error exporting ${format.toUpperCase()}`);
        });
    }
    
    // Auto-refresh data every 30 seconds
    let refreshInterval;
    
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            refreshTradingData();
        }, 30000);
    }
    
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    }
    
    function refreshTradingData() {
        const tableRows = document.querySelectorAll('.table tbody tr');
        
        // Add subtle loading indication
        tableRows.forEach(row => {
            row.style.opacity = '0.8';
        });
        
        makeRequest('/dashboard/trading-data/refresh', {
            method: 'GET'
        }).then(response => {
            if (response.success) {
                // Update table data
                updateTableData(response.data);
            }
        }).catch(error => {
            console.error('Refresh error:', error);
        }).finally(() => {
            // Remove loading indication
            tableRows.forEach(row => {
                row.style.opacity = '1';
            });
        });
    }
    
    function updateTableData(data) {
        // Update statistics
        if (data.stats) {
            updateStats(data.stats);
        }
        
        // Update table rows
        if (data.trading_data) {
            const tbody = document.querySelector('.table tbody');
            data.trading_data.forEach((item, index) => {
                const row = tbody.children[index];
                if (row) {
                    updateRowData(row, item);
                }
            });
        }
    }
    
    function updateStats(stats) {
        // Update stat values with animation
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"] .stat-value`);
            if (element) {
                animateValue(element, stats[key]);
            }
        });
    }
    
    function updateRowData(row, data) {
        // Update price
        const priceCell = row.querySelector('.price-cell');
        if (priceCell) {
            const oldPrice = parseFloat(priceCell.textContent.replace('$', ''));
            const newPrice = data.price;
            
            priceCell.textContent = `$${newPrice.toFixed(2)}`;
            
            // Add flash effect for price changes
            if (newPrice > oldPrice) {
                priceCell.classList.add('price-positive');
                flashElement(priceCell, '#d1fae5');
            } else if (newPrice < oldPrice) {
                priceCell.classList.add('price-negative');
                flashElement(priceCell, '#fee2e2');
            }
        }
        
        // Update volume bar
        const volumeBar = row.querySelector('.volume-fill');
        if (volumeBar && data.volume) {
            const percentage = Math.min(100, (data.volume / 10000000) * 100);
            volumeBar.style.width = `${percentage}%`;
        }
        
        // Update timestamp
        const timeCell = row.querySelector('td:last-child div:first-child');
        if (timeCell) {
            const now = new Date();
            timeCell.textContent = now.toLocaleTimeString('en-US', { 
                hour12: false, 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
        }
    }
    
    function flashElement(element, color) {
        const originalBackground = element.style.backgroundColor;
        element.style.backgroundColor = color;
        element.style.transition = 'background-color 0.3s ease';
        
        setTimeout(() => {
            element.style.backgroundColor = originalBackground;
        }, 300);
    }
    
    function animateValue(element, newValue) {
        const currentValue = parseFloat(element.textContent.replace(/[^0-9.-]/g, ''));
        const increment = (newValue - currentValue) / 20;
        let current = currentValue;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= newValue) || (increment < 0 && current <= newValue)) {
                current = newValue;
                clearInterval(timer);
            }
            element.textContent = formatStatValue(current);
        }, 50);
    }
    
    function formatStatValue(value) {
        if (value >= 1000000) {
            return `$${(value / 1000000).toFixed(1)}M`;
        } else if (value >= 1000) {
            return `$${(value / 1000).toFixed(1)}K`;
        } else {
            return `$${value.toFixed(2)}`;
        }
    }
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Stop auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
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