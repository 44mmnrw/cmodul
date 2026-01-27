@extends('layout')

@section('title', $place->name)

@section('content')
<div class="place-detail-page">
    <!-- Header -->
    <div class="detail-header">
        <a href="{{ route('places.index') }}" class="back-button">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"/>
            </svg>
        </a>
        <div class="header-content">
            <h1 class="detail-title">{{ $place->name }}</h1>
            <div class="header-badge">{{ $place->place_id }}</div>
        </div>
        <div class="header-actions">
            <a href="{{ route('places.edit', $place->place_id) }}" class="btn-secondary">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                Редактировать
            </a>
            <form action="{{ route('places.destroy', $place->place_id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" onclick="return confirm('Удалить это место?')">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/>
                    </svg>
                    Удалить
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="detail-container">
        <!-- Left Column -->
        <div class="detail-left">
            <!-- Information Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Информация</h2>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Код места</label>
                        <div class="info-value">{{ $place->place_id }}</div>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Название</label>
                        <div class="info-value">{{ $place->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Stock Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Остаток</h2>
                </div>
                <div class="stock-display">
                    <div class="stock-metric">
                        <div class="metric-label">Текущий остаток</div>
                        <div class="metric-value">{{ $place->stock?->quantity ?? 0 }}</div>
                        <div class="metric-unit">шт</div>
                    </div>
                    <div class="stock-metric reserved">
                        <div class="metric-label">Зарезервировано</div>
                        <div class="metric-value">{{ $place->stock?->reserved_quantity ?? 0 }}</div>
                        <div class="metric-unit">шт</div>
                    </div>
                </div>
            </div>

            <!-- Components Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Используемые компоненты</h2>
                </div>
                @if($place->details->count() > 0)
                    <div class="components-list">
                        @foreach($place->details as $detail)
                            <div class="component-item">
                                <a href="{{ route('details.show', $detail) }}" class="component-link">
                                    {{ $detail->name }}
                                </a>
                                <span class="component-qty">{{ $detail->pivot->quantity ?? 1 }} шт</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <p>Компоненты не найдены</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <aside class="detail-right">
            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Статистика</h2>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Всего компонентов</span>
                    <span class="stat-value">{{ $place->details->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Доступный остаток</span>
                    <span class="stat-value">{{ ($place->stock?->quantity ?? 0) - ($place->stock?->reserved_quantity ?? 0) }}</span>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
    .place-detail-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* Header */
    .detail-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .back-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #6a7282;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .back-button:hover {
        border-color: #d1d5db;
        background-color: #f9fafb;
        color: #101828;
    }

    .back-button svg {
        width: 20px;
        height: 20px;
    }

    .header-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-title {
        font-size: 28px;
        font-weight: 700;
        color: #101828;
        margin: 0;
        line-height: 36px;
    }

    .header-badge {
        font-family: monospace;
        font-size: 12px;
        color: #6a7282;
        background: #f3f4f6;
        padding: 4px 8px;
        border-radius: 4px;
        width: fit-content;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .btn-secondary, .btn-danger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        background: white;
        color: #101828;
    }

    .btn-secondary:hover {
        border-color: #d1d5db;
        background-color: #f9fafb;
    }

    .btn-danger {
        color: #ef4444;
        border-color: #fecaca;
    }

    .btn-danger:hover {
        background-color: #fef2f2;
    }

    /* Container */
    .detail-container {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 24px;
    }

    /* Card */
    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #101828;
        margin: 0;
        line-height: 20px;
    }

    /* Info Grid */
    .info-grid {
        padding: 24px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 12px;
        color: #6a7282;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 16px;
        color: #101828;
        font-weight: 400;
        line-height: 24px;
    }

    /* Stock */
    .stock-display {
        padding: 24px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
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

    .metric-label {
        font-size: 12px;
        color: #6a7282;
        font-weight: 400;
    }

    .metric-value {
        font-size: 24px;
        font-weight: 700;
        color: #101828;
    }

    .metric-unit {
        font-size: 12px;
        color: #6a7282;
    }

    /* Components */
    .components-list {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .component-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
        gap: 12px;
    }

    .component-link {
        color: #155dfc;
        text-decoration: none;
        font-weight: 500;
        flex: 1;
    }

    .component-link:hover {
        text-decoration: underline;
    }

    .component-qty {
        font-size: 12px;
        color: #6a7282;
        white-space: nowrap;
    }

    .empty-state {
        padding: 24px;
        text-align: center;
        color: #6a7282;
    }

    /* Right Column */
    .detail-right {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .stat-item {
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        font-size: 12px;
        color: #6a7282;
        font-weight: 500;
    }

    .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: #101828;
    }

    @media (max-width: 768px) {
        .detail-container {
            grid-template-columns: 1fr;
        }

        .detail-header {
            flex-wrap: wrap;
        }

        .stock-display {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
