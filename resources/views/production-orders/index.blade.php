@extends('layout')

@section('title', 'Производственные заказы')

@section('content')
<div class="production-orders-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Производственные заказы</h1>
            <p class="page-subtitle">Размещение и управление заказами на производство шкафов</p>
        </div>
        <a href="{{ route('production-orders.create') }}" class="btn-primary btn-create-config">
            <svg class="btn-icon">
                <use xlink:href="#icon-plus"></use>
            </svg>
            <span>Новый заказ</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего</p>
                <p class="stat-value">{{ $stats['total'] }}</p>
            </div>
            <svg class="stat-icon stat-icon-primary">
                <use xlink:href="#icon-lightning"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Ожидает</p>
                <p class="stat-value">{{ $stats['ordering'] }}</p>
            </div>
            <svg class="stat-icon stat-icon-warning">
                <use xlink:href="#icon-clock"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">В работе</p>
                <p class="stat-value">{{ $stats['in_production'] }}</p>
            </div>
            <svg class="stat-icon stat-icon-info">
                <use xlink:href="#icon-settings"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Готов</p>
                <p class="stat-value">{{ $stats['ready'] }}</p>
            </div>
            <svg class="stat-icon stat-icon-success">
                <use xlink:href="#icon-check"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Завершен</p>
                <p class="stat-value">{{ $stats['completed'] }}</p>
            </div>
            <svg class="stat-icon stat-icon-success">
                <use xlink:href="#icon-verified"></use>
            </svg>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card filters-card">
        <div class="filters-header">
            <svg class="filter-icon">
                <use xlink:href="#icon-filter"></use>
            </svg>
            <span class="filter-label">Статус:</span>
            <form method="GET" class="filter-buttons-group">
                <button type="submit" name="status" value="all" class="filter-button {{ $currentStatus === 'all' ? 'active' : '' }}">
                    Все
                </button>
                <button type="submit" name="status" value="ordering" class="filter-button {{ $currentStatus === 'ordering' ? 'active' : '' }}">
                    Ожидает
                </button>
                <button type="submit" name="status" value="in_production" class="filter-button {{ $currentStatus === 'in_production' ? 'active' : '' }}">
                    В работе
                </button>
                <button type="submit" name="status" value="ready" class="filter-button {{ $currentStatus === 'ready' ? 'active' : '' }}">
                    Готов
                </button>
                <button type="submit" name="status" value="completed" class="filter-button {{ $currentStatus === 'completed' ? 'active' : '' }}">
                    Завершен
                </button>
            </form>
        </div>

        <div class="search-wrapper">
            <svg class="search-icon">
                <use xlink:href="#icon-search"></use>
            </svg>
            <form method="GET" class="search-form">
                <input type="text" name="search" value="{{ $search }}" placeholder="Поиск по номеру заказа, конфигурации или примечаниям..." class="search-input">
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card production-orders-card">
        <div class="card-header">
            <h3 class="card-title">Список заказов</h3>
        </div>

        @if($orders->count())
            <table class="production-table">
                <thead>
                    <tr>
                        <th>Номер заказа</th>
                        <th>Дата / Срок</th>
                        <th>План. дата</th>
                        <th class="text-center">Конфигов</th>
                        <th class="text-center">Кол-во</th>
                        <th class="text-right">Сумма</th>
                        <th class="text-center">Статус</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong class="order-number">{{ $order->order->order_num }}</strong>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <span class="date-value">{{ $order->created_at->format('d.m.Y') }}</span>
                                    <span class="date-deadline">до {{ $order->created_at->addMonths(2)->format('d.m.Y') }}</span>
                                </div>
                            </td>
                            <td>
                                @if($order->planned_date)
                                    <span class="planned-date">{{ \Carbon\Carbon::parse($order->planned_date)->format('d.m.Y') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="qty-value">{{ $order->config_count }} шт</span>
                            </td>
                            <td class="text-center">
                                <span class="qty-value">{{ $order->total_quantity }} шт</span>
                            </td>
                            <td class="text-right">
                                <strong class="price-value">{{ number_format($order->total_quantity * 13550, 0, ',', ' ') }} ₽</strong>
                            </td>
                            <td class="text-center">
                                @if($order->orderStatus)
                                    <span class="status-badge" style="background-color: {{ $order->orderStatus->color }};">
                                        {{ $order->orderStatus->name }}
                                    </span>
                                @else
                                    <span class="status-badge">Не указан</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('production-orders.show', $order->id) }}" class="btn-icon btn-view" title="Просмотр">
                                        <svg class="icon">
                                            <use xlink:href="#icon-arrow-right"></use>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($orders->hasPages())
                <div class="pagination-wrapper">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg class="empty-icon">
                    <use xlink:href="#icon-inbox"></use>
                </svg>
                <p class="empty-message">Заказы не найдены</p>
            </div>
        @endif
    </div>
</div>
@endsection
