@extends('layout')

@section('content')
<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="title-section">
                <h1 class="page-title">Виртуальные остатки конфигураций</h1>
                <p class="page-subtitle">Рассчитанные остатки готовых конфигураций на основе доступных компонентов</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Configs -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего конфигураций</p>
                <p class="stat-value">{{ $stats['total_configs'] ?? 0 }}</p>
            </div>
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Общая стоимость</p>
                <p class="stat-value">{{ number_format($stats['total_cost'] ?? 0, 0, '.', ' ') }} ₽</p>
            </div>
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4-2.67z"/>
                </svg>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Низкий запас</p>
                <p class="stat-value stat-value-warning">{{ $stats['low_stock'] ?? 0 }}</p>
            </div>
            <div class="stat-icon stat-icon-warning">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 4c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2s2-.9 2-2v-4c0-1.1-.9-2-2-2z"/>
                </svg>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Нет в наличии</p>
                <p class="stat-value stat-value-danger">{{ $stats['out_of_stock'] ?? 0 }}</p>
            </div>
            <div class="stat-icon stat-icon-danger">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card search-filter-card">
        <form method="GET" action="{{ route('stock.virtual-stock') }}" class="search-form">
            <div class="search-input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input 
                    type="text" 
                    name="search"
                    class="search-input" 
                    placeholder="Поиск по названию, коду или описанию..."
                    value="{{ request('search') }}"
                >
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="card table-card">
        <table class="configurations-table">
            <thead>
                <tr>
                    <th class="col-config">Конфигурация</th>
                    <th class="col-stock">Виртуальный остаток</th>
                    <th class="col-status">Статус</th>
                    <th class="col-limiting">Лимитирующий компонент</th>
                    <th class="text-right col-price">Цена за шт.</th>
                    <th class="text-right col-total">Общая стоимость</th>
                </tr>
            </thead>
                <tbody>
                    @forelse($configurations as $config)
                    <tr>
                        <td>
                            <div class="config-cell">
                                <div class="config-name">{{ $config['name'] }}</div>
                                <div class="config-code">{{ $config['scu'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="stock-display">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="stock-icon">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6l5.25 3.15-.75 1.23-4.5-2.67z"/>
                                </svg>
                                <span class="stock-number">{{ $config['virtual_stock'] }}</span>
                                <span class="stock-unit">шт</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $config['status_class'] ?? 'success' }}">{{ $config['status'] ?? 'Неизвестно' }}</span>
                        </td>
                        <td>
                            <div class="limiting-component">
                                <div class="component-name">{{ $config['limiting_component'] ?? '—' }}</div>
                                @if($config['limiting_component'] !== 'N/A' && $config['limiting_component'] !== null)
                                <div class="component-info">Доступно: {{ $config['limiting_available'] ?? 0 }} шт</div>
                                <div class="component-info">Требуется на 1 шт: {{ $config['limiting_required'] ?? 1 }} шт</div>
                                @endif
                            </div>
                        </td>
                        <td class="text-right">
                            <span class="price">{{ $config['unit_price'] > 0 ? number_format($config['unit_price'], 0, '.', ' ') . ' ₽' : '—' }}</span>
                        </td>
                        <td class="text-right">
                            <span class="price">{{ $config['total_price'] > 0 ? number_format($config['total_price'], 0, '.', ' ') . ' ₽' : '—' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            <p style="color: #666;">Нет конфигураций для отображения</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- Info Block -->
    <div class="info-block">
        <svg class="info-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 4c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2s2-.9 2-2v-4c0-1.1-.9-2-2-2z"/>
        </svg>
        <div class="info-content">
            <h4 class="info-title">Как рассчитываются виртуальные остатки?</h4>
            <p class="info-description">
                Виртуальный остаток показывает, сколько готовых конфигураций можно собрать из имеющихся на складе компонентов. 
                Система автоматически определяет лимитирующий компонент — компонент с наименьшим остатком относительно требуемого количества.
            </p>
        </div>
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }

    .title-section {
        flex: 1;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 0.9375rem;
        color: #666;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.8125rem;
        color: #999;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .stat-value-warning {
        color: #ff9500;
    }

    .stat-value-danger {
        color: #f44336;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        margin-left: 1rem;
    }

    .stat-icon-blue {
        background: #2196f3;
    }

    .stat-icon-green {
        background: #4caf50;
    }

    .stat-icon-warning {
        background: #ff9500;
    }

    .stat-icon-danger {
        background: #f44336;
    }

    .stat-icon svg {
        width: 24px;
        height: 24px;
    }

    .search-filter-card {
        margin-bottom: 2rem;
    }

    .search-form {
        width: 100%;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        width: 20px;
        height: 20px;
        color: #999;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #2196f3;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }

    .card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .table-card {
        margin-bottom: 2rem;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .configurations-table {
        width: 100%;
        border-collapse: collapse;
    }

    .configurations-table thead {
        background: #f5f5f5;
        border-bottom: 2px solid #e0e0e0;
    }

    .configurations-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        color: #666;
        text-transform: uppercase;
    }

    .col-config {
        width: 35%;
    }

    .col-stock {
        width: 12%;
    }

    .col-status {
        width: 12%;
    }

    .col-limiting {
        width: 20%;
    }

    .col-price {
        width: 11%;
    }

    .col-total {
        width: 10%;
    }

    .configurations-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
        font-size: 0.9375rem;
    }

    .configurations-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .configurations-table tbody tr:hover {
        background: #fafafa;
    }

    .config-cell {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .config-name {
        font-weight: 600;
        color: #1a1a1a;
    }

    .config-code {
        font-size: 0.8125rem;
        color: #999;
        font-family: 'Courier New', monospace;
    }

    .config-description {
        font-size: 0.8125rem;
        color: #999;
    }

    .stock-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stock-icon {
        width: 20px;
        height: 20px;
        color: #2196f3;
        flex-shrink: 0;
    }

    .stock-number {
        font-weight: 700;
        font-size: 1rem;
        color: #1a1a1a;
    }

    .stock-unit {
        font-size: 0.8125rem;
        color: #999;
    }

    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 4px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-success {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-warning {
        background: #fff3e0;
        color: #e65100;
    }

    .status-danger {
        background: #ffebee;
        color: #c62828;
    }

    .limiting-component {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .component-name {
        font-weight: 500;
        color: #1a1a1a;
    }

    .component-info {
        font-size: 0.8125rem;
        color: #999;
    }

    .text-right {
        text-align: right;
    }

    .price {
        font-weight: 600;
        color: #1a1a1a;
        font-family: 'Courier New', monospace;
    }

    .info-block {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 8px;
        padding: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .info-icon {
        width: 24px;
        height: 24px;
        color: #1976d2;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .info-content {
        flex: 1;
    }

    .info-title {
        font-weight: 600;
        color: #1976d2;
        margin: 0 0 0.5rem 0;
        font-size: 0.9375rem;
    }

    .info-description {
        margin: 0;
        font-size: 0.875rem;
        color: #1565c0;
        line-height: 1.5;
    }

    .content-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1.5rem;
    }
</style>
@endsection
