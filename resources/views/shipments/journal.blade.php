@extends('layout')

@section('content')
<div class="shipments-wrapper">
    <!-- Header Section -->
    <div class="shipments-header">
        <div class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </div>
        <div class="header-content">
            <div class="header-icon-box blue">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 5h6v2H9V5m0 4h6v2H9V9M7 2h10c1.1 0 2 .9 2 2v16c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2m0 2v16h10V4H7z"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>Отгрузка шкафов</h1>
                <p>Регистрация отгрузки готовых шкафов клиентам</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('shipments.create') }}" class="btn-new-shipment">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Новая отгрузка
        </a>
        <button class="btn-history">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
            </svg>
            История отгрузок
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Shipments -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего отгрузок</p>
                <p class="stat-value">{{ $stats['total_shipments'] ?? 0 }}</p>
            </div>
            <div class="stat-icon blue-bg">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.16-2.66c-.3-.37-.77-.56-1.24-.56-.99 0-1.57 1.14-.82 1.89l2.98 3.67c.35.41.87.67 1.41.67.54 0 1.06-.26 1.41-.67l4.15-5.23c.75-.75.17-1.89-.82-1.89-.48 0-.95.19-1.25.56z"/>
                </svg>
            </div>
        </div>

        <!-- Total Quantity -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Отгружено шкафов</p>
                <p class="stat-value">{{ $stats['total_quantity'] ?? 0 }} <span class="unit">шт</span></p>
            </div>
            <div class="stat-icon green-bg">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Общая сумма</p>
                <p class="stat-value">{{ number_format($stats['total_cost'] ?? 0, 0, '.', ' ') }} ₽</p>
            </div>
            <div class="stat-icon purple-bg">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <div class="filter-period">
            <span class="period-label">Период:</span>
            <button class="period-btn active">Все время</button>
            <button class="period-btn">Сегодня</button>
            <button class="period-btn">Неделя</button>
            <button class="period-btn">Месяц</button>
        </div>

        <div class="search-box">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input 
                type="text" 
                class="search-input" 
                placeholder="Поиск по клиенту, конфигурации или примечаниям..."
            >
        </div>
    </div>

    <!-- Journal Table -->
    <div class="journal-card">
        <div class="journal-header">
            <h3>Журнал отгрузок</h3>
        </div>
        <div class="table-wrapper">
            <table class="shipments-table">
                <thead>
                    <tr>
                        <th class="col-doc-number">Номер документа</th>
                        <th class="col-date">Дата</th>
                        <th class="col-client">Клиент</th>
                        <th class="col-quantity">Шкафов</th>
                        <th class="col-amount">Сумма</th>
                        <th class="col-actions">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $shipment)
                    <tr class="shipment-row" onclick="toggleDetails(this)">
                        <td class="col-doc-number">{{ $shipment['number'] ?? 'ОТ-2025-001' }}</td>
                        <td class="col-date">
                            <div class="date-cell">
                                <svg class="date-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                </svg>
                                {{ $shipment['date'] ?? '28.01.2025' }}
                            </div>
                        </td>
                        <td class="col-client">
                            <div class="client-info">
                                <div class="client-name">{{ $shipment['client'] ?? 'ООО "Компания"' }}</div>
                                @if($shipment['address'] ?? false)
                                <div class="client-address">{{ $shipment['address'] }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="col-quantity">
                            <span class="quantity-badge">{{ $shipment['quantity'] ?? 0 }} шт</span>
                        </td>
                        <td class="col-amount">{{ number_format($shipment['amount'] ?? 0, 0, '.', ' ') }} ₽</td>
                        <td class="col-actions">
                            <button class="icon-btn">
                                <svg class="icon-eye" viewBox="0 0 46 33.45">
                                    <use xlink:href="#icon-eye"></use>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Details Row -->
                    <tr class="details-row" style="display: none;">
                        <td colspan="6">
                            <div class="details-table-wrapper">
                                <table class="details-table">
                                    <thead>
                                        <tr>
                                            <th>Название компонента</th>
                                            <th>Код</th>
                                            <th class="text-center">Количество</th>
                                            <th class="text-right">Цена за шт.</th>
                                            <th class="text-right">Сумма</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shipment['items'] ?? [] as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td><code>{{ $item['code'] }}</code></td>
                                            <td class="text-center">{{ $item['quantity'] }} шт</td>
                                            <td class="text-right">{{ number_format($item['unit_price'], 0, '.', ' ') }} ₽</td>
                                            <td class="text-right"><strong>{{ number_format($item['sum'], 0, '.', ' ') }} ₽</strong></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <p>Нет данных для отображения</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/shipments.css') }}">

<script>
function toggleDetails(row) {
    const detailsRow = row.nextElementSibling;
    if (detailsRow && detailsRow.classList.contains('details-row')) {
        const isVisible = detailsRow.style.display !== 'none';
        detailsRow.style.display = isVisible ? 'none' : 'table-row';
        row.classList.toggle('expanded', !isVisible);
    }
}
</script>

@endsection