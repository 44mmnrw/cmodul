@extends('layout')

@section('content')
<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="title-section">
                <h1 class="page-title">Конфигурации шкафов</h1>
                <p class="page-subtitle">Управление готовыми конфигурациями шкафов</p>
            </div>
            <button class="btn-primary btn-lg">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Создать конфигурацию
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="configs-stats-grid">
        <!-- Total Configs -->
        <div class="config-stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего конфигураций</p>
                <p class="stat-value">{{ count($cabinets) }}</p>
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
                <p class="stat-value stat-value-success">{{ count($cabinets) }}</p>
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
                                <a href="/configurations/{{ $cabinet->id }}" class="action-btn" title="Просмотреть">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor">
                                        <path d="M8 3C4 3 1 8 1 8s3 5 7 5 7-5 7-5-3-5-7-5z" stroke-width="1.5"/>
                                        <circle cx="8" cy="8" r="2" stroke-width="1.5"/>
                                    </svg>
                                </a>
                                <button class="action-btn" title="Редактировать">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor">
                                        <path d="M3 13h10M11.5 2.5l-8 8" stroke-width="1.5"/>
                                    </svg>
                                </button>
                                <button class="action-btn" title="Дублировать">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor">
                                        <rect x="1" y="4" width="9" height="9"/>
                                        <path d="M5 1h8v8"/>
                                    </svg>
                                </button>
                                <button class="action-btn" title="Удалить">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor">
                                        <path d="M13 4H3M5 7v5M8 7v5M11 7v5M5 2v1H2v1h12V3h-3V2"/>
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
    </div>
</div>
@endsection
