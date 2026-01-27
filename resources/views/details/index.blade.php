@extends('layout')

@section('title', 'Детали')

@section('content')
<div class="details-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Детали</h1>
            <p class="page-subtitle">Управление деталями и компонентами</p>
        </div>
        <a href="{{ route('details.create') }}" class="btn-primary">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
            </svg>
            Добавить деталь
        </a>
    </div>

    <!-- Details Table -->
    <div class="card details-card">
        <div class="table-wrapper">
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>ID</th>
                        <th>Категория</th>
                        <th>Размеры (ШxВxГ)</th>
                        <th>Вес</th>
                        <th>Цена</th>
                        <th>Дата создания</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                        <tr>
                            <td>
                                <a href="{{ route('details.show', $detail) }}" class="detail-link">
                                    {{ $detail->name }}
                                </a>
                            </td>
                            <td>
                                <code class="detail-code">{{ $detail->id }}</code>
                            </td>
                            <td>
                                @if($detail->category)
                                    <span class="category-badge">{{ $detail->category->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                {{ $detail->width ?? '—' }} × {{ $detail->height ?? '—' }} × {{ $detail->depth ?? '—' }} мм
                            </td>
                            <td>
                                {{ $detail->weight ?? '—' }} кг
                            </td>
                            <td>
                                @if($detail->price)
                                    <span class="price-value">{{ $detail->price }} ₽</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                {{ $detail->created_at->format('d.m.Y') }}
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('details.show', $detail) }}" class="btn-icon btn-view" title="Просмотр">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('details.edit', $detail) }}" class="btn-icon btn-edit" title="Редактировать">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('details.destroy', $detail) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" title="Удалить" onclick="return confirm('Вы уверены?')">
                                            <svg viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Детали не найдены. <a href="{{ route('details.create') }}">Добавить новую</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($details) && method_exists($details, 'hasPages') && $details->hasPages())
        <div class="pagination-wrapper">
            <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
                <!-- Previous Page Link -->
                @if ($details->onFirstPage())
                    <span class="pagination-item disabled">← Назад</span>
                @else
                    <a href="{{ $details->previousPageUrl() }}" class="pagination-item">← Назад</a>
                @endif

                <!-- Pagination Elements -->
                <div class="pagination-numbers">
                    @foreach ($details->getUrlRange(1, $details->lastPage()) as $page => $url)
                        @if ($page == $details->currentPage())
                            <span class="pagination-item active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                <!-- Next Page Link -->
                @if ($details->hasMorePages())
                    <a href="{{ $details->nextPageUrl() }}" class="pagination-item">Далее →</a>
                @else
                    <span class="pagination-item disabled">Далее →</span>
                @endif
            </nav>
        </div>
    @endif
</div>

<style>
    /* Page Styles */
    .details-list-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
        align-items: center;
        padding: 24px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pagination-numbers {
        display: flex;
        gap: 4px;
    }

    .pagination-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        color: #155dfc;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination-item:hover:not(.disabled):not(.active) {
        border-color: #155dfc;
        background-color: #f0f4ff;
    }

    .pagination-item.active {
        background-color: #155dfc;
        color: white;
        border-color: #155dfc;
    }

    .pagination-item.disabled {
        color: #d1d5db;
        cursor: not-allowed;
        opacity: 0.5;
    }

    .pagination-info {
        font-size: 13px;
        color: #6a7282;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
    }

    .page-header-content {
        flex: 1;
    }

    .page-title {
        font-size: 32px;
        font-weight: bold;
        color: #101828;
        margin: 0 0 4px 0;
        line-height: 40px;
    }

    .page-subtitle {
        font-size: 16px;
        color: #6a7282;
        margin: 0;
        line-height: 24px;
    }

    /* Primary Button */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #2563eb;
        box-shadow: 0px 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary svg {
        width: 20px;
        height: 20px;
    }

    /* Card */
    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0px 1px 3px rgba(0,0,0,0.1), 0px 1px 2px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .details-card {
        width: 100%;
    }

    /* Table */
    .table-wrapper {
        overflow-x: auto;
    }

    .details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .details-table thead {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .details-table th {
        padding: 16px 24px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6a7282;
        text-transform: uppercase;
        line-height: 16px;
    }

    .details-table th.text-center {
        text-align: center;
    }

    .details-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s ease;
    }

    .details-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .details-table td {
        padding: 16px 24px;
        font-size: 14px;
        color: #101828;
        line-height: 20px;
    }

    .details-table td.text-center {
        text-align: center;
    }

    /* Links */
    .detail-link {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .detail-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .detail-code {
        background: #f3f4f6;
        color: #6a7282;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: 'Consolas', monospace;
        font-size: 12px;
    }

    /* Badges */
    .category-badge {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .price-value {
        font-weight: 600;
        color: #155dfc;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-icon:hover {
        background: #f9fafb;
        color: #374151;
    }

    .btn-icon svg {
        width: 16px;
        height: 16px;
    }

    .btn-view:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .btn-edit:hover {
        border-color: #f59e0b;
        color: #f59e0b;
    }

    .btn-delete:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    .delete-form {
        display: inline;
    }

    /* Utility Classes */
    .text-muted {
        color: #6a7282;
    }

    .text-center {
        text-align: center;
    }

    .py-4 {
        padding: 16px 0;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary {
            justify-content: center;
        }

        .details-table td,
        .details-table th {
            padding: 12px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 24px;
            line-height: 32px;
        }

        .page-header {
            gap: 16px;
        }

        .details-table {
            font-size: 12px;
        }

        .details-table thead {
            display: none;
        }

        .details-table tbody,
        .details-table tr,
        .details-table td {
            display: block;
            width: 100%;
        }

        .details-table tr {
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .details-table td {
            padding: 12px 16px;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .details-table td::before {
            content: attr(data-label);
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            color: #6a7282;
        }

        .details-table td.text-center {
            text-align: left;
        }

        .action-buttons {
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        .btn-icon {
            width: 100%;
        }
    }
</style>
@endsection
