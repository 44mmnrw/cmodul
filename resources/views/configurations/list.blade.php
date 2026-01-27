@extends('layout')

@section('content')
<div class="content-wrapper">
    <!-- Type Switcher -->
    <div class="type-switcher">
        <a href="{{ route('configurations.index', ['type' => 1]) }}" 
           class="type-switcher-btn {{ $currentType == 1 ? 'active' : '' }}">
            Конфигурации шкафов
        </a>
        <a href="{{ route('configurations.index', ['type' => 2]) }}" 
           class="type-switcher-btn {{ $currentType == 2 ? 'active' : '' }}">
            Конфигурации мест
        </a>
    </div>

    <!-- Header Section -->
    <div class="page-header">
        <div class="config-header-content">
            <div class="title-section">
                <h1 class="page-title">
                    @if($currentType == 1)
                        Конфигурации шкафов
                    @else
                        Комплектующие
                    @endif
                </h1>
                <p class="page-subtitle">
                    @if($currentType == 1)
                        Управление готовыми конфигурациями шкафов
                    @else
                        Управление доступными комплектующими
                    @endif
                </p>
            </div>
        </div>
        <button onclick="window.location.href='{{ route('configurations.create', ['type' => $currentType]) }}'" class="btn-primary btn-create-config">
            <svg class="btn-icon">
                <use xlink:href="#icon-plus"></use>
            </svg>
            <span>Создать конфигурацию</span>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="configs-stats-grid">
        <!-- Total Configs -->
        <div class="config-stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего конфигураций</p>
                <p class="stat-value">{{ method_exists($cabinets, 'total') ? $cabinets->total() : count($cabinets) }}</p>
            </div>
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                </svg>
            </div>
        </div>

        <!-- Available -->
        <div class="config-stat-card">
            <div class="stat-content">
                <p class="stat-label">Доступно</p>
                <p class="stat-value stat-value-success">{{ method_exists($cabinets, 'total') ? $cabinets->total() : count($cabinets) }}</p>
            </div>
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="config-stat-card">
            <div class="stat-content">
                <p class="stat-label">Общая стоимость</p>
                <p class="stat-value">86к ₽</p>
            </div>
            <div class="stat-icon stat-icon-purple">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3-12h-3V9c0-.55-.45-1-1-1s-1 .45-1 1v1H9c-.55 0-1 .45-1 1s.45 1 1 1h3v1H9c-.55 0-1 .45-1 1s.45 1 1 1h1v1c0 .55.45 1 1 1s1-.45 1-1v-1h3c.55 0 1-.45 1-1s-.45-1-1-1h-3v-1h3c.55 0 1-.45 1-1s-.45-1-1-1z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card search-filter-card">
        <div class="search-input-wrapper">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input 
                type="text" 
                class="search-input" 
                placeholder="Поиск по названию, коду или описанию..."
            >
        </div>
    </div>

    <!-- Table Section -->
    <div class="card table-card">
        <div class="table-wrapper">
            <table class="configurations-table">
                <thead>
                    <tr>
                        <th>Конфигурация</th>
                        <th>Компоненты</th>
                        <th>Стоимость</th>
                        <th>Статус</th>
                        <th>Дата создания</th>
                        <th class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cabinets as $cabinet)
                    <tr style="cursor: pointer;" onclick="window.location.href='/configurations/{{ $cabinet->id }}'">
                        <td>
                            <div class="config-cell">
                                <a href="/configurations/{{ $cabinet->id }}" class="config-name-link" style="text-decoration: none; color: inherit;">
                                    <div class="config-name">{{ $cabinet->name }}</div>
                                    <div class="config-code">{{ $cabinet->scu }}</div>
                                    <div class="config-description">{{ $cabinet->description ?? 'Описание конфигурации' }}</div>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="components-list">
                                <span class="component-badge component-badge-blue">Основание: 1</span>
                                <span class="component-badge component-badge-green">Панели: 2</span>
                                <span class="component-badge component-badge-purple">Двери: 1</span>
                            </div>
                        </td>
                        <td>
                            <span class="price">{{ $cabinet->price ?? '19 900 ₽' }}</span>
                        </td>
                        <td>
                            <span class="status-badge status-available">
                                <svg class="status-icon" viewBox="0 0 12 12" fill="currentColor">
                                    <circle cx="6" cy="6" r="4"/>
                                </svg>
                                Доступно
                            </span>
                        </td>
                        <td>
                            <span class="created-date">{{ $cabinet->created_at->format('Y-m-d') ?? '2024-01-25' }}</span>
                        </td>
                        <td class="text-right">
                            <div class="action-buttons">
                                <a href="/configurations/{{ $cabinet->id }}" class="action-btn btn-view" title="Просмотреть">
                                    <svg class="icon">
                                        <use xlink:href="#icon-eye"></use>
                                    </svg>
                                </a>
                                <a href="{{ route('configurations.edit', $cabinet->id) }}" class="action-btn btn-edit" title="Редактировать">
                                    <svg class="icon">
                                        <use xlink:href="#icon-pencil"></use>
                                    </svg>
                                </a>
                                <button class="action-btn btn-delete" title="Удалить" onclick="openDeleteModal(event, '/configurations/{{ $cabinet->id }}')">
                                    <svg class="icon">
                                        <use xlink:href="#icon-delete"></use>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            Нет конфигураций
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($cabinets, 'total'))
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Показано {{ method_exists($cabinets, 'from') ? $cabinets->from() : 1 }}-{{ method_exists($cabinets, 'to') ? $cabinets->to() : count($cabinets) }} из {{ $cabinets->total() }} конфигураций
            </div>
            <div class="pagination-controls">
                @if (method_exists($cabinets, 'onFirstPage') && $cabinets->onFirstPage())
                    <button class="pagination-btn pagination-btn-disabled" disabled>
                        <svg viewBox="0 0 16 16" fill="currentColor">
                            <path d="M10.5 12.5L5 8l5.5-4.5"/>
                        </svg>
                        Назад
                    </button>
                @elseif(method_exists($cabinets, 'previousPageUrl'))
                    <a href="{{ $cabinets->previousPageUrl() }}" class="pagination-btn">
                        <svg viewBox="0 0 16 16" fill="currentColor">
                            <path d="M10.5 12.5L5 8l5.5-4.5"/>
                        </svg>
                        Назад
                    </a>
                @endif

                <div class="pagination-numbers">
                    @if(method_exists($cabinets, 'currentPage'))
                        @if ($cabinets->currentPage() > 2)
                            <a href="{{ $cabinets->url(1) }}" class="pagination-number">1</a>
                        @endif

                        @if ($cabinets->currentPage() > 3)
                            <span class="pagination-dots">...</span>
                        @endif

                        @foreach ($cabinets->getUrlRange(max(1, $cabinets->currentPage() - 1), min($cabinets->lastPage(), $cabinets->currentPage() + 1)) as $page => $url)
                            @if ($page == $cabinets->currentPage())
                                <span class="pagination-number pagination-number-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($cabinets->currentPage() < $cabinets->lastPage() - 2)
                            <span class="pagination-dots">...</span>
                        @endif

                        @if ($cabinets->currentPage() < $cabinets->lastPage() - 1)
                            <a href="{{ $cabinets->url($cabinets->lastPage()) }}" class="pagination-number">{{ $cabinets->lastPage() }}</a>
                        @endif
                    @endif
                </div>

                @if (method_exists($cabinets, 'hasMorePages') && $cabinets->hasMorePages())
                    <a href="{{ $cabinets->nextPageUrl() }}" class="pagination-btn">
                        Далее
                        <svg viewBox="0 0 16 16" fill="currentColor">
                            <path d="M5.5 12.5L11 8l-5.5-4.5"/>
                        </svg>
                    </a>
                @else
                    <button class="pagination-btn pagination-btn-disabled" disabled>
                        Далее
                        <svg viewBox="0 0 16 16" fill="currentColor">
                            <path d="M5.5 12.5L11 8l-5.5-4.5"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Modal Component -->
<x-delete-modal 
    warningText="Вы уверены, что хотите удалить конфигурацию?"
    warningSubtext="Это действие невозможно отменить. Все компоненты конфигурации также будут удалены."
/>
@endsection
