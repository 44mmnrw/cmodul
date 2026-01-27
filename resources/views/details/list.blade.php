@extends('layout')

@section('title', $title ?? 'Список')

@section('content')
<div class="details-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ $pageTitle ?? 'Список' }}</h1>
            <p class="page-subtitle">{{ $pageSubtitle ?? 'Управление элементами' }}</p>
        </div>
        @if($addButtonUrl ?? false)
            <a href="{{ $addButtonUrl }}" class="btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                </svg>
                {{ $addButtonText ?? 'Добавить' }}
            </a>
        @endif
    </div>

    <!-- Details Table -->
    <div class="card details-card">
        <div class="table-wrapper">
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Код</th>
                        <th>Название</th>
                        <th class="text-right">Остаток</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->category)
                                    <span class="category-badge">{{ $item->category->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <code class="detail-code">{{ $item->scu }}</code>
                            </td>
                            <td>
                                <a href="{{ route('details.show', $item) }}" class="detail-link">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td class="text-right">
                                <span class="text-muted">—</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('details.show', $item) }}" class="btn-icon btn-view" title="Просмотр">
                                        <svg class="icon">
                                            <use xlink:href="#icon-eye"></use>
                                        </svg>
                                    </a>
                                    <a href="{{ route('details.edit', $item) }}" class="btn-icon btn-edit" title="Редактировать">
                                        <svg class="icon">
                                            <use xlink:href="#icon-pencil"></use>
                                        </svg>
                                    </a>
                                    <form action="{{ route('details.destroy', $item) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" title="Удалить" onclick="return confirm('Вы уверены?')">
                                            <svg class="icon">
                                                <use xlink:href="#icon-delete"></use>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                {{ $emptyMessage ?? 'Элементы не найдены.' }} <a href="{{ $addButtonUrl ?? '#' }}">Добавить новый</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($items) && method_exists($items, 'hasPages') && $items->hasPages())
        <div class="pagination-wrapper">
            <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
                <!-- Previous Page Link -->
                @if ($items->onFirstPage())
                    <span class="pagination-item disabled">← Назад</span>
                @else
                    <a href="{{ $items->previousPageUrl() }}" class="pagination-item">← Назад</a>
                @endif

                <!-- Pagination Elements -->
                <div class="pagination-numbers">
                    @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                        @if ($page == $items->currentPage())
                            <span class="pagination-item active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                <!-- Next Page Link -->
                @if ($items->hasMorePages())
                    <a href="{{ $items->nextPageUrl() }}" class="pagination-item">Далее →</a>
                @else
                    <span class="pagination-item disabled">Далее →</span>
                @endif
            </nav>
        </div>
    @endif
</div>

@endsection
