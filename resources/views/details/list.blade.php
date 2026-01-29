@extends('layout')

@section('title', $title ?? 'Список')

@section('content')
<div class="details-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ $pageTitle ?? 'Список' }}</h1>
            <p class="page-subtitle">{{ $pageSubtitle ?? 'Управление элементами' }}</p>
            
            @if(isset($currentType) && isset($typeNames))
            <div class="type-selector" style="margin-top: 12px;">
                @foreach($typeNames as $typeId => $typeName)
                    <a href="/items?type={{ $typeId }}" class="type-button {{ $currentType == $typeId ? 'active' : '' }}" style="
                        display: inline-block;
                        padding: 8px 16px;
                        margin-right: 8px;
                        border-radius: 6px;
                        border: 1px solid #e5e7eb;
                        background: {{ $currentType == $typeId ? '#155dfc' : '#fff' }};
                        color: {{ $currentType == $typeId ? '#fff' : '#4a5565' }};
                        text-decoration: none;
                        font-size: 14px;
                        font-weight: 500;
                        transition: all 0.2s;
                        cursor: pointer;
                    ">{{ $typeName }}</a>
                @endforeach
            </div>
            @endif
        </div>
        @if($addButtonUrl ?? false)
            <button onclick="window.location.href='{{ $addButtonUrl }}'" class="btn-primary btn-create-config">
                <svg class="btn-icon">
                    <use xlink:href="#icon-plus"></use>
                </svg>
                <span>{{ $addButtonText ?? 'Добавить' }}</span>
            </button>
        @endif
    </div>

    <!-- Details Table -->
    <div class="card details-card">
        <table class="details-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ $sortBaseUrl }}?sort=category_id&direction={{ $currentSort === 'category_id' && $currentDirection === 'asc' ? 'desc' : 'asc' }}" class="sortable-header">
                            Категория
                            @if($currentSort === 'category_id')
                                <span class="sort-indicator">{{ $currentDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ $sortBaseUrl }}?sort=scu&direction={{ $currentSort === 'scu' && $currentDirection === 'asc' ? 'desc' : 'asc' }}" class="sortable-header">
                            Код
                            @if($currentSort === 'scu')
                                <span class="sort-indicator">{{ $currentDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ $sortBaseUrl }}?sort=name&direction={{ $currentSort === 'name' && $currentDirection === 'asc' ? 'desc' : 'asc' }}" class="sortable-header">
                            Название
                            @if($currentSort === 'name')
                                <span class="sort-indicator">{{ $currentDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
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
                                @if($item->stock)
                                    <span class="stock-quantity">{{ $item->stock->quantity ?? 0 }}</span>
                                    <span class="stock-unit"> шт</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
