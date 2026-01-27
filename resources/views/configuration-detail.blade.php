@extends('layout')

@section('content')
<div class="config-detail-wrapper">
    <!-- Header Section -->
    <div class="config-detail-header">
        <button class="btn-back" title="Вернуться назад">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="config-detail-header-content">
            <h1 class="config-detail-title">{{ $cabinet->name ?? 'Шкаф напольный 42U стандарт' }}</h1>
            <div class="config-detail-meta">
                <span class="config-code">{{ $cabinet->scu ?? 'CAB-42U-600-ST' }}</span>
                <span class="status-badge status-available">
                    <svg class="status-icon" viewBox="0 0 12 12" fill="currentColor">
                        <circle cx="6" cy="6" r="4"/>
                    </svg>
                    Доступно
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="config-detail-grid">
        <!-- Left Column -->
        <div class="config-detail-left">
            <!-- Description Card -->
            <div class="card config-card">
                <div class="config-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 4a1 1 0 011-1h6a1 1 0 011 1v14a1 1 0 01-1 1H3a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="card-title">Описание</h3>
                </div>
                <p class="config-description">
                    {{ $cabinet->description ?? 'Стандартный шкаф 42U с основанием 600x600, боковыми панелями 2000мм и стеклянной дверью' }}
                </p>
            </div>

            <!-- Virtual Stock Card -->
            <div class="card config-card card-virtual-stock">
                <div class="virtual-stock-header">
                    <div>
                        <h3 class="card-title">Виртуальный остаток</h3>
                        <p class="card-subtitle">Доступно для сборки</p>
                    </div>
                    <svg class="stock-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="virtual-stock-body">
                    <div class="stock-quantity">
                        <div class="quantity-number">32</div>
                        <div class="quantity-unit">шкафов</div>
                    </div>
                    <div class="stock-info">
                        <div class="stock-status">
                            <div class="status-label">Высокий запас</div>
                            <div class="limiting-info">
                                <p class="limiting-text">Лимитирует: Дверь стеклянная 2000мм</p>
                                <p class="limiting-text">Доступно 32 шт, требуется 1 шт на шкаф</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Components Table -->
            <div class="card config-card">
                <div class="config-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="card-title">Состав конфигурации</h3>
                </div>
                <div class="table-wrapper">
                    <table class="components-table">
                        <thead>
                            <tr>
                                <th>Тип</th>
                                <th>Компонент</th>
                                <th>Характеристики</th>
                                <th class="text-center">Кол-во</th>
                                <th class="text-center">Остаток</th>
                                <th class="text-right">Цена</th>
                                <th class="text-right">Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cabinet->componentsInConfiguration as $index => $component)
                            @php
                                $types = ['blue', 'green', 'purple', 'orange', 'red'];
                                $typeNames = ['Основание', 'Боковая панель', 'Дверь', 'Панель', 'Крышка'];
                                $typeIndex = $index % count($types);
                                $typeName = $typeNames[$typeIndex] ?? 'Компонент';
                                $typeClass = 'component-type-' . $types[$typeIndex];
                            @endphp
                            <tr @if($index === 2) class="row-warning" @endif style="cursor: pointer;" onclick="window.location.href='/places/{{ $component->scu }}'">
                                <td>
                                    <span class="component-type-badge {{ $typeClass }}">{{ $typeName }}</span>
                                </td>
                                <td>
                                    <a href="/places/{{ $component->scu }}" style="text-decoration: none; color: inherit;">
                                        <div class="component-info">
                                            <div class="component-name">{{ $component->name }}</div>
                                            <div class="component-code">{{ $component->scu }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <div class="component-specs">
                                        <div class="spec">{{ $component->description ?? 'Компонент' }}</div>
                                    </div>
                                </td>
                                <td class="text-center">1</td>
                                <td class="text-center">-</td>
                                <td class="text-right">{{ $component->price ?? '0 ₽' }}</td>
                                <td class="text-right">{{ $component->price ?? '0 ₽' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">
                                    Нет компонентов в конфигурации
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="config-detail-right">
            <!-- Cost Card -->
            <div class="card config-card">
                <div class="config-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.5 5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM15 5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM8.5 15a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM15 15a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <h3 class="card-title">Стоимость</h3>
                </div>
                <div class="cost-details">
                    <div class="cost-row">
                        <span class="cost-label">Расчетная стоимость:</span>
                        <span class="cost-value">19 400 ₽</span>
                    </div>
                    <div class="cost-row">
                        <span class="cost-label">Конфигурация:</span>
                        <span class="cost-value">19 900 ₽</span>
                    </div>
                    <div class="cost-row">
                        <span class="cost-label">Разница:</span>
                        <span class="cost-value cost-value-positive">-500 ₽</span>
                    </div>
                    <div class="cost-total">
                        <span class="cost-label">Итого:</span>
                        <span class="cost-total-value">19 900 ₽</span>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card config-card">
                <div class="config-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="card-title">Статистика</h3>
                </div>
                <div class="stats-details">
                    <div class="stat-row">
                        <span class="stat-label">Всего компонентов:</span>
                        <span class="stat-value">4 шт</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Уникальных типов:</span>
                        <span class="stat-value">3</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Дата создания:</span>
                        <span class="stat-value">{{ $cabinet->created_at->format('Y-m-d') ?? '2024-01-25' }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Макс. запас:</span>
                        <span class="stat-value">32 шт</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Общая стоимость запаса:</span>
                        <span class="stat-value">636 800 ₽</span>
                    </div>
                </div>
            </div>

            <!-- Schema Card -->
            <div class="card config-card">
                <h3 class="card-title">Схема шкафа</h3>
                <div class="cabinet-schema">
                    <div class="schema-cabinet">
                        <div class="cabinet-body">
                            <div class="cabinet-door">
                                <div class="door-label">Дверь<br><span class="door-qty">1 шт</span></div>
                            </div>
                        </div>
                        <div class="cabinet-legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background: #155dfc;"></div>
                                <span>Основание (1)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: #00a63e;"></div>
                                <span>Панели (2)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: #4a5565;"></div>
                                <span>Двери (1)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
