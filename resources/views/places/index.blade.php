@extends('layout')

@section('title', 'Места')

@section('content')
<div class="places-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Места размещения</h1>
            <p class="page-subtitle">Управление местами и размещением компонентов</p>
        </div>
        <a href="{{ route('places.create') }}" class="btn-primary">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
            </svg>
            Добавить место
        </a>
    </div>

    <!-- Places Table -->
    <div class="card places-card">
        <div class="table-wrapper">
            <table class="places-table">
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Название</th>
                        <th class="text-center">Компонентов</th>
                        <th class="text-center">Остаток</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($places as $place)
                        <tr>
                            <td>
                                <code class="place-code">{{ $place->place_id }}</code>
                            </td>
                            <td>
                                <a href="{{ route('places.show', $place->place_id) }}" class="place-link">
                                    {{ $place->name }}
                                </a>
                            </td>
                            <td class="text-center">
                                {{ $place->details_count ?? $place->details()->count() }}
                            </td>
                            <td class="text-center">
                                {{ $place->stock?->quantity ?? 0 }} шт
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('places.show', $place->place_id) }}" class="btn-icon btn-view" title="Просмотр">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('places.edit', $place->place_id) }}" class="btn-icon btn-edit" title="Редактировать">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('places.destroy', $place->place_id) }}" method="POST" class="delete-form">
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                Места не найдены. <a href="{{ route('places.create') }}">Добавить новое</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Page Styles */
    .places-list-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
    }

    .page-header-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: #101828;
        margin: 0;
        line-height: 40px;
    }

    .page-subtitle {
        font-size: 16px;
        color: #6a7282;
        margin: 0;
        line-height: 24px;
    }

    /* Card */
    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    /* Table */
    .table-wrapper {
        overflow-x: auto;
    }

    .places-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .places-table thead {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .places-table th {
        padding: 16px 24px;
        text-align: left;
        font-size: 12px;
        font-weight: 500;
        color: #6a7282;
        line-height: 16px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .places-table th.text-center {
        text-align: center;
    }

    .places-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    .places-table tbody tr:last-child {
        border-bottom: none;
    }

    .places-table tbody tr:hover {
        background-color: #fafafa;
    }

    .places-table td {
        padding: 16px 24px;
        font-size: 14px;
        color: #101828;
        line-height: 20px;
    }

    .places-table td.text-center {
        text-align: center;
    }

    .place-code {
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 12px;
        color: #6a7282;
        background: #f3f4f6;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .place-link {
        color: #155dfc;
        text-decoration: none;
        font-weight: 500;
    }

    .place-link:hover {
        text-decoration: underline;
    }

    /* Buttons */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #155dfc;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #0d47bb;
    }

    .btn-primary svg {
        width: 20px;
        height: 20px;
    }

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
        color: #6a7282;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-icon:hover {
        color: #101828;
        border-color: #d1d5db;
    }

    .btn-icon svg {
        width: 16px;
        height: 16px;
    }

    .btn-view:hover {
        color: #155dfc;
    }

    .btn-edit:hover {
        color: #f59e0b;
    }

    .btn-delete:hover {
        color: #ef4444;
        border-color: #fecaca;
        background: #fef2f2;
    }

    .delete-form {
        display: contents;
    }

    .text-muted {
        color: #6a7282;
    }

    .py-4 {
        padding: 16px 0;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .page-title {
            font-size: 24px;
        }

        .places-table th,
        .places-table td {
            padding: 12px 16px;
            font-size: 12px;
        }
    }
</style>
@endsection
