@extends('layout')

@section('title', 'Производственный заказ ' . $orders->first()?->order->order_num)

@section('content')
<div class="production-order-detail-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Производственный заказ</h1>
            <p class="page-subtitle">{{ $orders->first()?->order->order_num }} · {{ $orders->first()?->created_at->format('d.m.Y') }}</p>
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
            <a href="{{ route('production-orders.edit', $orders->first()) }}" class="btn-primary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-pencil"></use>
                </svg>
                <span>Редактировать</span>
            </a>
        </div>
    </div>

    <!-- Order Info Table -->
    <div class="order-info-table-wrapper">
        <table class="order-info-table">
            <tbody>
                <tr>
                    <td class="info-label">Дата заказа</td>
                    <td class="info-value">{{ $orders->first()?->created_at->format('d.m.Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Примечания</td>
                    <td class="info-value">{{ $orders->first()?->notes ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Статус</td>
                    <td class="info-value">
                        @if($orders->first()?->orderStatus)
                            <span class="status-badge" style="background-color: {{ $orders->first()?->orderStatus->color }};">
                                {{ $orders->first()?->orderStatus->name }}
                            </span>
                        @else
                            <span class="text-muted">Не указан</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Configurations Table -->
    <div class="configs-list-wrapper">
        <h3 class="configs-list-title">Конфигурации к производству</h3>
        <div class="table-wrapper">
            <table class="configs-list-table">
                <thead>
                    <tr>
                        <th class="col-number">№</th>
                        <th class="col-config">Конфигурация</th>
                        <th class="col-quantity">Заказано</th>
                        <th class="col-received">Получено</th>
                        <th class="col-date">Плановая дата</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                        <tr>
                            <td class="col-number">{{ $index + 1 }}</td>
                            <td class="col-config">{{ $order->product->name }}</td>
                            <td class="col-quantity">{{ $order->quantity_ordered }} шт</td>
                            <td class="col-received">{{ $order->quantity_received }} шт</td>
                            <td class="col-date">
                                @if($order->planned_date)
                                    {{ $order->planned_date->format('d.m.Y') }}
                                @else
                                    <span class="text-muted">не установлена</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Нет конфигураций</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Button -->
    <div class="mt-6">
        <form action="{{ route('production-orders.destroy', $orders->first()) }}" method="POST" class="delete-form" onsubmit="return confirm('Вы уверены? Это действие необратимо.')">
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

