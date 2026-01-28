@extends('layout')

@section('title', 'Производственный заказ ПЗ-' . $order->id)

@section('content')
<div class="production-order-detail-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Производственный заказ</h1>
            <p class="page-subtitle">ПЗ-{{ $order->id }} · {{ $order->created_at->format('d.m.Y') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('production-orders.index') }}" class="btn-secondary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-arrow-left"></use>
                </svg>
                <span>Назад</span>
            </a>
            <a href="{{ route('production-orders.create') }}" class="btn-secondary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-plus"></use>
                </svg>
                <span>Новый заказ</span>
            </a>
            <a href="{{ route('production-orders.edit', $order) }}" class="btn-primary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-pencil"></use>
                </svg>
                <span>Редактировать</span>
            </a>
        </div>
    </div>

    <!-- Order Info Cards -->
    <div class="order-info-grid">
        <div class="info-card">
            <span class="info-label">Основной компонент</span>
            <p class="info-value">{{ $order->product->name }}</p>
        </div>
        <div class="info-card">
            <span class="info-label">Кол-во заказано</span>
            <p class="info-value">{{ $order->quantity_ordered }} шт</p>
        </div>
        <div class="info-card">
            <span class="info-label">Получено</span>
            <p class="info-value">{{ $order->quantity_received }} шт</p>
        </div>
        <div class="info-card">
            <span class="info-label">Статус</span>
            <p class="info-value">
                @php
                    $statusClass = [
                        'ordering' => 'status-ordering',
                        'in_production' => 'status-in-production',
                        'ready' => 'status-ready',
                        'completed' => 'status-completed',
                    ][$order->status] ?? 'status-default';
                    $statusLabel = [
                        'ordering' => 'Ожидает',
                        'in_production' => 'В производстве',
                        'ready' => 'Готов',
                        'completed' => 'Завершен',
                    ][$order->status] ?? $order->status;
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </p>
        </div>
        <div class="info-card">
            <span class="info-label">Плановая дата</span>
            <p class="info-value">
                @if($order->planned_date)
                    {{ $order->planned_date->format('d.m.Y') }}
                @else
                    <span class="text-muted">не установлена</span>
                @endif
            </p>
        </div>
        <div class="info-card">
            <span class="info-label">Срок исполнения</span>
            <p class="info-value">до {{ $order->created_at->addMonths(2)->format('d.m.Y') }}</p>
        </div>
    </div>

    <!-- Notes Section -->
    @if($order->notes)
        <div class="card notes-card">
            <div class="card-header">
                <h3 class="card-title">Примечания</h3>
            </div>
            <div class="card-body">
                <p class="notes-text">{{ $order->notes }}</p>
            </div>
        </div>
    @endif

    <!-- Receive Quantity Form -->
    @if($order->getPendingQuantity() > 0 && $order->status !== 'completed')
        <div class="card receive-card">
            <div class="card-header">
                <h3 class="card-title">Приемка товара</h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Осталось получить: <strong>{{ $order->getPendingQuantity() }} шт</strong></p>
                <form action="{{ route('production-orders.receiveQuantity', $order) }}" method="POST" class="receive-form">
                    @csrf
                    <div class="form-group">
                        <label for="quantity" class="form-label">Количество для приемки</label>
                        <input type="number" name="quantity" id="quantity" class="form-input" min="1" max="{{ $order->getPendingQuantity() }}" required>
                    </div>
                    <button type="submit" class="btn-primary">Принять товар</button>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Button -->
    <div class="mt-6">
        <form action="{{ route('production-orders.destroy', $order) }}" method="POST" class="delete-form" onsubmit="return confirm('Вы уверены? Это действие необратимо.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">
                <svg class="btn-icon">
                    <use xlink:href="#icon-delete"></use>
                </svg>
                <span>Удалить заказ</span>
            </button>
        </form>
    </div>
</div>
@endsection

