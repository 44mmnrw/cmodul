@extends('layout')

@section('title', 'Статусы производственных заказов')

@section('content')
<div class="statuses-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Статусы производственных заказов</h1>
            <p class="page-subtitle">Управление справочником статусов</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('production-orders.index') }}" class="btn-secondary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-arrow-left"></use>
                </svg>
                <span>Назад</span>
            </a>
            <a href="{{ route('production-order-statuses.create') }}" class="btn-primary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-plus"></use>
                </svg>
                <span>Добавить статус</span>
            </a>
        </div>
    </div>

    <!-- Statuses Table -->
    <div class="table-wrapper">
        <table class="statuses-table">
            <thead>
                <tr>
                    <th class="col-code">Код</th>
                    <th class="col-name">Название</th>
                    <th class="col-color">Цвет</th>
                    <th class="col-description">Описание</th>
                    <th class="col-order">Порядок</th>
                    <th class="col-actions">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statuses as $status)
                    <tr>
                        <td class="col-name">{{ $status->name }}</td>
                            <td class="col-color">
                                <div class="color-preview">
                                    <span class="color-box" style="background-color: {{ $status->color }}"></span>
                                    <code>{{ $status->color }}</code>
                                </div>
                            </td>
                            <td class="col-description">
                                @if($status->description)
                                    {{ Str::limit($status->description, 50) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="col-order">{{ $status->sort_order }}</td>
                            <td class="col-actions">
                            <div class="actions-group">
                                <a href="{{ route('production-order-statuses.edit', $status) }}" class="btn-icon-link" title="Редактировать">
                                    <svg class="btn-icon">
                                        <use xlink:href="#icon-pencil"></use>
                                    </svg>
                                </a>
                                <form action="{{ route('production-order-statuses.destroy', $status) }}" method="POST" class="inline-form" onsubmit="return confirm('Вы уверены?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-link btn-danger" title="Удалить">
                                        <svg class="btn-icon">
                                            <use xlink:href="#icon-delete"></use>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Статусы не найдены
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.statuses-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    gap: 24px;
}

.page-header-content {
    flex: 1;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #101828;
    margin: 0 0 8px 0;
}

.page-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.btn-secondary, .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.btn-secondary {
    background: white;
    border: 1px solid #d1d5dc;
    color: #364153;
}

.btn-secondary:hover {
    background: #f9fafb;
}

.btn-primary {
    background: #4f39f6;
    color: white;
}

.btn-primary:hover {
    background: #4330d9;
}

.btn-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.table-wrapper {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}

.statuses-table {
    width: 100%;
    border-collapse: collapse;
}

.statuses-table thead {
    background-color: #f3f4f6;
    border-bottom: 2px solid #e5e7eb;
}

.statuses-table thead th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.statuses-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
    transition: background-color 0.2s ease;
}

.statuses-table tbody tr:hover {
    background-color: #f9fafb;
}

.statuses-table tbody tr:last-child {
    border-bottom: none;
}

.statuses-table td {
    padding: 14px 16px;
    font-size: 14px;
    color: #374151;
}

.col-code {
    width: 120px;
}

.col-name {
    width: 180px;
}

.col-color {
    width: 150px;
}

.col-order {
    width: 80px;
    text-align: center;
}

.col-actions {
    width: 100px;
    text-align: right;
}

.status-code {
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-family: monospace;
    color: #6b7280;
}

.status-name-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.color-display {
    display: flex;
    align-items: center;
    gap: 8px;
}

.color-box {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    border: 1px solid #e5e7eb;
}

.color-code {
    font-family: monospace;
    font-size: 13px;
    color: #6b7280;
}

.actions-group {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.btn-icon-link {
    display: inline-flex;
    align-items: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #6b7280;
}

.btn-icon-link:hover {
    background: #f3f4f6;
    color: #374151;
}

.btn-icon-link.btn-danger {
    color: #991b1b;
}

.btn-icon-link.btn-danger:hover {
    background: #fee2e2;
}

.inline-form {
    display: inline;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: #9ca3af;
}
</style>
@endsection
