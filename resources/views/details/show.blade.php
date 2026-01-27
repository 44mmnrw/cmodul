@extends('layout')

@section('title', $detail->name . ' - Детали')

@section('content')
<div class="detail-page">
    <!-- Header Section with Back Button -->
    <div class="page-header">
        <div class="header-back-button">
            <a href="{{ route('details.index') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5">
                    <path fill-rule="evenodd" d="M12 19l-8-8 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <div class="header-content">
            <div class="header-title-row">
                <h1 class="header-title">{{ $detail->name }}</h1>
                @if($detail->category)
                    <span class="category-badge">{{ $detail->category->name }}</span>
                @endif
            </div>
            <p class="header-code">{{ $detail->scu ?? 'PROD-001' }}</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="header-actions">
            <button onclick="document.getElementById('productionIntakeModal').classList.remove('hidden')" 
                    class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1 4.5 4.5 0 11-4.514 6.949z"/>
                </svg>
                Регистрация прихода
            </button>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="main-grid">
        <!-- Left Column -->
        <div class="left-column">
            
            <!-- Source Card -->
            <div class="card">
                <div class="card-body">
                    <div class="source-section">
                        <div class="source-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="source-content">
                            <h3 class="section-title">Источник</h3>
                            <div class="source-badge">
                                <svg class="badge-icon" viewBox="0 0 16 16" fill="currentColor">
                                    <circle cx="8" cy="8" r="7"/>
                                </svg>
                                <span>{{ $detail->source->name ?? 'Не указан' }}</span>
                            </div>
                            <p class="source-description">
                                @if($detail->source && $detail->source->name === 'Покупка')
                                    Деталь приобретается у поставщиков. Зависит от наличия на складе поставщика и сроков доставки.
                                @else
                                    Деталь производится на собственных мощностях. Полный контроль над производственным процессом и сроками изготовления.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Characteristics Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                    </svg>
                    <h3 class="section-title">Характеристики</h3>
                </div>
                <div class="characteristics-grid">
                    <div class="char-item">
                        <span class="char-label">Размеры</span>
                        <span class="char-value">{{ $detail->width }}x{{ $detail->height }}x{{ $detail->depth }}мм</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Материал</span>
                        <span class="char-value">{{ $detail->material ?? 'Сталь холоднокатаная' }}</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Вес</span>
                        <span class="char-value">{{ $detail->weight }} кг</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Единица измерения</span>
                        <span class="char-value">{{ $detail->scu ?? 'шт' }}</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Категория</span>
                        <span class="char-value">{{ $detail->category->name ?? 'Общее' }}</span>
                    </div>
                </div>
            </div>

            <!-- Components Table Card -->
            <div class="card table-card">
                <div class="card-header-with-count">
                    <div class="header-left">
                        <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 000-2H7zM7 7a1 1 0 000 2h6a1 1 0 000-2H7zM7 11a1 1 0 100 2h6a1 1 0 100-2H7zM3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 7.324a2 2 0 01-1.952 2.41H6.54a2 2 0 00-1.952 2.41l.833 4.89A2 2 0 018.41 22h5.18a2 2 0 001.946-1.57l1-5.9"/>
                        </svg>
                        <h3 class="section-title">Используется в компонентах</h3>
                    </div>
                    <span class="count-badge">({{ count($detail->places) }})</span>
                </div>

                <div class="table-wrapper">
                    <table class="components-table">
                        <thead>
                            <tr>
                                <th>ТИП</th>
                                <th>КОМПОНЕНТ</th>
                                <th class="center">КОЛ-ВО НА 1 ШТ</th>
                                <th class="right">ЦЕНА КОМПОНЕНТА</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detail->places as $place)
                                <tr class="place-row" onclick="window.location.href = '{{ route('places.show', $place) }}';" style="cursor: pointer;">
                                    <td>
                                        <span class="type-badge">Боковая панель</span>
                                    </td>
                                    <td>
                                        <div class="component-info">
                                            <div class="component-name">{{ $place->name }}</div>
                                            <span class="component-code">{{ $place->place_id }}</span>
                                        </div>
                                    </td>
                                    <td class="center">{{ $place->pivot->quantity }} шт</td>
                                    <td class="right">{{ number_format($detail->price ?? 0, 0, ',', ' ') }} ₽</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Деталь не используется в компонентах
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock Analysis Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="section-title">Анализ остатков</h3>
                </div>

                <div class="stock-analysis-grid">
                    <div class="stock-metric">
                        <span class="metric-label">Физический остаток</span>
                        <span class="metric-value">210</span>
                        <span class="metric-unit">шт</span>
                    </div>
                    <div class="stock-metric reserved">
                        <span class="metric-label">Зарезервировано</span>
                        <span class="metric-value">735</span>
                        <span class="metric-unit">шт</span>
                    </div>
                    <div class="stock-metric available">
                        <span class="metric-label">Доступно</span>
                        <span class="metric-value">-525</span>
                        <span class="metric-unit">шт</span>
                    </div>
                </div>

                <!-- Critical Alert -->
                <div class="alert-critical">
                    <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="alert-content">
                        <h4 class="alert-title">Критический дефицит!</h4>
                        <p class="alert-text">Текущего запаса недостаточно для обеспечения всех компонентов на складе. Необходимо пополнение на 525 шт.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            
            <!-- Stock Summary Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 12a9 9 0 1 0 18 0A9 9 0 0 0 3 12Z"/>
                    </svg>
                    <h3 class="section-title">Остатки</h3>
                </div>

                <div class="stock-item">
                    <span class="stock-label">Текущий остаток</span>
                    <div class="stock-value-display">
                        <span class="value-number">210</span>
                        <span class="value-unit">шт</span>
                    </div>
                </div>

                <div class="stock-item divider">
                    <span class="stock-label">Общая стоимость запаса</span>
                    <span class="stock-price-value">88 200 ₽</span>
                </div>
            </div>

            <!-- Cost Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.5 11a1 1 0 11-2 0 1 1 0 012 0zM12.5 11a1 1 0 11-2 0 1 1 0 012 0zM5 8a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                    <h3 class="section-title">Стоимость</h3>
                </div>

                <div class="cost-list">
                    <div class="cost-row">
                        <span class="cost-label">Цена за шт:</span>
                        <span class="cost-value">{{ number_format($detail->price ?? 420, 0, ',', ' ') }} ₽</span>
                    </div>
                    <div class="cost-row divider">
                        <span class="cost-label">Цена за кг:</span>
                        <span class="cost-value">{{ number_format($detail->price_per_kg ?? 350, 2, ',', ' ') }} ₽</span>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="section-title">Статистика</h3>
                </div>

                <div class="stats-list">
                    <div class="stat-row">
                        <span class="stat-label">Компонентов:</span>
                        <span class="stat-value">{{ $detail->places->count() }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Зарезервировано:</span>
                        <span class="stat-value">735 шт</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Дата создания:</span>
                        <span class="stat-value">{{ $detail->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Источник:</span>
                        <span class="stat-value">Свое пр-во</span>
                    </div>
                </div>
            </div>

            <!-- Visualization Card -->
            <div class="card">
                <h3 class="section-title">Визуализация</h3>
                <div class="visualization-container">
                    <div class="visualization-item">
                        <div class="visualization-box">
                            <svg class="visualization-icon" viewBox="0 0 64 64" fill="currentColor">
                                <path d="M32 8L8 20v24c0 13.25 24 20 24 20s24-6.75 24-20V20L32 8z" opacity="0.2"/>
                                <path d="M32 12L12 22v20c0 10 20 16 20 16s20-6 20-16V22L32 12z" fill="none" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="vis-label">{{ $detail->category->name ?? 'Металл' }}</span>
                        <span class="vis-unit">{{ $detail->scu ?? 'шт' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* Page Container */
    .detail-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* Header */
    .page-header {
        display: flex;
        gap: 16px;
        height: 60px;
        align-items: center;
        width: 100%;
        justify-content: space-between;
    }

    .header-back-button {
        flex-shrink: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: white;
        border: 1px solid #e5e7eb;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-back:hover {
        background: #f9fafb;
        color: #374151;
    }

    .btn-back svg {
        width: 20px;
        height: 20px;
    }

    .header-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .header-title-row {
        display: flex;
        gap: 12px;
        align-items: center;
        height: 32px;
    }

    .header-title {
        font-size: 24px;
        font-weight: 700;
        color: #101828;
        margin: 0;
        line-height: 32px;
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        background: #f3f4f6;
        color: #364153;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        height: fit-content;
    }

    .header-actions {
        flex-shrink: 0;
        display: flex;
        gap: 8px;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: #155dfc;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #0d47a1;
    }
    }

    .header-code {
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 14px;
        color: #6a7282;
        margin: 0;
        line-height: 20px;
    }

    /* Main Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 389px;
        gap: 24px;
        width: 100%;
    }

    .left-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .right-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .table-card {
        overflow: visible;
    }

    /* Card Headers */
    .card-header,
    .card-header-with-count {
        display: flex;
        gap: 12px;
        align-items: center;
        font-size: 16px;
        font-weight: 600;
        color: #101828;
    }

    .card-header-with-count {
        justify-content: space-between;
    }

    .header-left {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .card-icon {
        width: 20px;
        height: 20px;
        color: #6b7280;
        flex-shrink: 0;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #101828;
        margin: 0;
        line-height: 24px;
    }

    .count-badge {
        font-size: 14px;
        color: #6a7282;
        font-weight: 400;
        line-height: 20px;
    }

    /* Source Section */
    .source-section {
        display: flex;
        gap: 16px;
    }

    .source-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        background: #dbeafe;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0284c7;
    }

    .source-icon svg {
        width: 24px;
        height: 24px;
    }

    .source-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .source-badge {
        background: #eff6ff;
        border-radius: 10px;
        padding: 8px 12px;
        display: inline-flex;
        gap: 8px;
        align-items: center;
        width: fit-content;
    }

    .badge-icon {
        width: 16px;
        height: 16px;
        color: #1447e6;
    }

    .source-badge span {
        font-size: 14px;
        color: #1447e6;
        font-weight: 500;
    }

    .source-description {
        font-size: 14px;
        color: #4a5565;
        line-height: 20px;
        margin: 0;
    }

    /* Characteristics Grid */
    .characteristics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        width: 100%;
    }

    .char-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .char-label {
        font-size: 12px;
        color: #6a7282;
        font-weight: 400;
        line-height: 16px;
        text-transform: uppercase;
    }

    .char-value {
        font-size: 14px;
        color: #101828;
        font-weight: 400;
        line-height: 20px;
    }

    /* Table */
    .table-wrapper {
        overflow-x: auto;
        margin: 0 -24px -24px -24px;
    }

    .components-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
    }

    .components-table thead {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .components-table th {
        padding: 16px 24px;
        text-align: left;
        font-size: 12px;
        font-weight: 500;
        color: #6a7282;
        line-height: 16px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .components-table th.center {
        text-align: center;
    }

    .components-table th.right {
        text-align: right;
    }

    .components-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    .components-table tbody tr:last-child {
        border-bottom: none;
    }

    .components-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .components-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .components-table tbody tr:last-child {
        border-bottom: none;
    }

    .components-table td {
        padding: 24px;
        font-size: 14px;
        color: #101828;
        line-height: 20px;
    }

    .components-table td.center {
        text-align: center;
    }

    .components-table td.right {
        text-align: right;
    }

    .components-table td.text-center {
        text-align: center;
    }

    .components-table td.text-muted {
        color: #6a7282;
    }

    .type-badge {
        display: inline-block;
        background: #dcfce7;
        color: #008236;
        padding: 6px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .component-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .component-name {
        font-size: 14px;
        color: #101828;
        font-weight: 400;
        line-height: 20px;
        text-decoration: none;
    }

    .component-name:hover {
        color: #3b82f6;
        text-decoration: underline;
    }

    .component-code {
        font-family: 'Consolas', monospace;
        font-size: 12px;
        color: #6a7282;
        line-height: 16px;
    }

    /* Stock Analysis */
    .stock-analysis-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        width: 100%;
    }

    .stock-metric {
        background: #f9fafb;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stock-metric.reserved {
        background: #fff7ed;
    }

    .stock-metric.available {
        background: #fef2f2;
    }

    .metric-label {
        font-size: 12px;
        color: #4a5565;
        font-weight: 400;
        line-height: 16px;
    }

    .stock-metric.reserved .metric-label {
        color: #f54900;
    }

    .stock-metric.available .metric-label {
        color: #e7000b;
    }

    .metric-value {
        font-size: 24px;
        font-weight: 700;
        color: #101828;
        line-height: 32px;
    }

    .stock-metric.reserved .metric-value {
        color: #ca3500;
    }

    .stock-metric.available .metric-value {
        color: #c10007;
    }

    .metric-unit {
        font-size: 12px;
        color: #6a7282;
        font-weight: 400;
        line-height: 16px;
    }

    .stock-metric.reserved .metric-unit {
        color: #f54900;
    }

    .stock-metric.available .metric-unit {
        color: #e7000b;
    }

    /* Alert */
    .alert-critical {
        background: #fef2f2;
        border: 1px solid #ffc9c9;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }

    .alert-icon {
        width: 20px;
        height: 20px;
        color: #c10007;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .alert-title {
        font-size: 16px;
        color: #82181a;
        font-weight: 400;
        line-height: 24px;
        margin: 0;
    }

    .alert-text {
        font-size: 14px;
        color: #c10007;
        line-height: 20px;
        margin: 0;
    }

    /* Right Column - Stock Item */
    .stock-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stock-item.divider {
        border-top: 1px solid #e5e7eb;
        padding-top: 17px;
        margin-top: 17px;
    }

    .stock-label {
        font-size: 14px;
        color: #4a5565;
        font-weight: 400;
        line-height: 20px;
    }

    .stock-value-display {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .value-number {
        font-size: 30px;
        font-weight: 700;
        color: #101828;
        line-height: 36px;
    }

    .value-unit {
        font-size: 14px;
        color: #6a7282;
        line-height: 20px;
    }

    .stock-price-value {
        font-size: 24px;
        font-weight: 700;
        color: #155dfc;
        line-height: 32px;
    }

    /* Cost List */
    .cost-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .cost-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cost-row.divider {
        padding-top: 16px;
        border-top: none;
    }

    .cost-label {
        font-size: 14px;
        color: #4a5565;
        font-weight: 400;
        line-height: 20px;
    }

    .cost-value {
        font-size: 18px;
        font-weight: 700;
        color: #101828;
        line-height: 28px;
    }

    /* Stats List */
    .stats-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-label {
        font-size: 14px;
        color: #4a5565;
        font-weight: 400;
        line-height: 20px;
    }

    .stat-value {
        font-size: 14px;
        color: #101828;
        font-weight: 400;
        line-height: 20px;
    }

    /* Visualization */
    .visualization-container {
        background: #f9fafb;
        border-radius: 10px;
        padding: 40px 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 232px;
    }

    .visualization-item {
        display: flex;
        flex-direction: column;
        gap: 16px;
        align-items: center;
        width: 128px;
    }

    .visualization-box {
        background: #f3f4f6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 128px;
        height: 128px;
    }

    .visualization-icon {
        width: 64px;
        height: 64px;
        color: #6b7280;
    }

    .vis-label {
        font-size: 14px;
        color: #101828;
        font-weight: 400;
        line-height: 20px;
        text-align: center;
    }

    .vis-unit {
        font-size: 12px;
        color: #6a7282;
        font-weight: 400;
        line-height: 16px;
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .main-grid {
            grid-template-columns: 1fr;
        }

        .right-column {
            grid-column: 1;
        }

        .characteristics-grid {
            grid-template-columns: 1fr;
        }

        .stock-analysis-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-wrap: wrap;
            height: auto;
        }

        .header-title {
            font-size: 20px;
        }

        .card {
            padding: 16px;
        }

        .table-wrapper {
            margin: 0 -16px -16px -16px;
        }

        .components-table th,
        .components-table td {
            padding: 12px 16px;
            font-size: 12px;
        }

        .characteristics-grid,
        .stock-analysis-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Production Intake Modal -->
@include('modals.production-intake')

@endsection
