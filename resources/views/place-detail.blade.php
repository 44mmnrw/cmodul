@extends('layout')

@section('content')
<div class="place-detail-wrapper">
    <!-- Header Section -->
    <div class="place-detail-header">
        <a href="/places" class="btn-back" title="Вернуться назад">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="place-detail-header-content">
            <div class="place-header-title">
                <h1 class="place-title">{{ $place->name }}</h1>
                <span class="type-badge type-badge-base">{{ $place->category ?? 'Компонент' }}</span>
            </div>
            <p class="place-code">{{ $place->place_id }}</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="place-detail-grid">
        <!-- Left Column -->
        <div class="place-detail-left">
            <!-- Characteristics Card -->
            <div class="card place-card">
                <div class="place-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 4a1 1 0 011-1h6a1 1 0 011 1v14a1 1 0 01-1 1H3a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="card-title">Характеристики компонента</h3>
                </div>
                <div class="characteristics-grid">
                    <div class="characteristic-item">
                        <span class="char-label">Высота</span>
                        <span class="char-value">{{ $place->height ?? '100 мм' }}</span>
                    </div>
                    <div class="characteristic-item">
                        <span class="char-label">Ширина</span>
                        <span class="char-value">{{ $place->width ?? '600 мм' }}</span>
                    </div>
                    <div class="characteristic-item">
                        <span class="char-label">Глубина</span>
                        <span class="char-value">{{ $place->depth ?? '600 мм' }}</span>
                    </div>
                    <div class="characteristic-item">
                        <span class="char-label">Материал</span>
                        <span class="char-value">{{ $place->material ?? 'Сталь оцинкованная' }}</span>
                    </div>
                    <div class="characteristic-item">
                        <span class="char-label">Цвет</span>
                        <span class="char-value">{{ $place->color ?? 'RAL 7035' }}</span>
                    </div>
                    <div class="characteristic-item">
                        <span class="char-label">Поставщик</span>
                        <span class="char-value">{{ $place->supplier ?? 'ТехПром' }}</span>
                    </div>
                </div>
            </div>

            <!-- Components Table -->
            <div class="card place-card">
                <div class="place-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="card-title">Состав компонента</h3>
                </div>
                <div class="table-wrapper">
                    <table class="components-table">
                        <thead>
                            <tr>
                                <th>Деталь</th>
                                <th>Категория</th>
                                <th>Характеристики</th>
                                <th class="text-center">Кол-во на 1 шт</th>
                                <th class="text-center">Остаток</th>
                                <th class="text-right">Цена за ед.</th>
                                <th class="text-right">Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($placeDetail)
                                @forelse($placeDetail->componentsInConfiguration as $component)
                                <tr class="detail-row" onclick="window.location.href='{{ route('details.show', $component) }}'">
                                    <td>
                                        <div class="detail-info">
                                            <div class="detail-name">{{ $component->name }}</div>
                                            <div class="detail-code">{{ $component->scu }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge category-metal">
                                            {{ $component->productType->name ?? 'Компонент' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="detail-specs">
                                            @if($component->width)
                                                <div class="spec">{{ $component->width }}x{{ $component->depth }}x{{ $component->height }}мм</div>
                                            @endif
                                            @if($component->weight)
                                                <div class="spec">Вес: {{ $component->weight }} кг</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px; color: #999;">
                                        Нет компонентов в составе
                                    </td>
                                </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px; color: #999;">
                                        Компонент не найден в системе деталей
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Used in Configurations -->
            <div class="card place-card">
                <div class="place-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7z"/>
                    </svg>
                    <h3 class="card-title">Используется в конфигурациях</h3>
                    <span class="config-count">
                        @if($placeDetail)
                            ({{ $placeDetail->usedInCabinets->count() ?? 0 }})
                        @else
                            (0)
                        @endif
                    </span>
                </div>
                <div class="configurations-list">
                    @if($placeDetail)
                        @forelse($placeDetail->usedInCabinets as $cabinet)
                        <a href="/configurations/{{ $cabinet->id }}" class="config-item">
                            <div class="config-info">
                                <div class="config-name">{{ $cabinet->name }}</div>
                                <div class="config-code">{{ $cabinet->scu }}</div>
                            </div>
                            <div class="config-price">{{ $cabinet->price ?? '0 ₽' }}</div>
                        </a>
                        @empty
                        <div class="config-item empty">
                            <p>Не используется ни в одной конфигурации</p>
                        </div>
                        @endforelse
                    @else
                        <div class="config-item empty">
                            <p>Компонент не найден в системе деталей</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="place-detail-right">
            <!-- Stock Card -->
            <div class="card place-card">
                <div class="place-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.5 5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM15 5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <h3 class="card-title">Остатки</h3>
                </div>
                <div class="stock-details">
                    <div class="stock-section">
                        <p class="stock-label">Физический остаток</p>
                        <div class="stock-number">{{ $place->stock->quantity ?? 45 }}</div>
                        <p class="stock-unit">шт</p>
                    </div>
                    <div class="stock-section stock-virtual">
                        <p class="stock-label">Виртуальный остаток</p>
                        <div class="stock-number">{{ $place->stock->quantity ?? 87 }}</div>
                        <p class="stock-note">Можно собрать из деталей</p>
                    </div>
                    <div class="info-box info-box-blue">
                        <svg class="info-icon" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 0a8 8 0 110 16A8 8 0 018 0z"/>
                        </svg>
                        <p>Виртуальный остаток показывает максимальное количество компонентов, которое можно собрать из имеющихся деталей</p>
                    </div>
                </div>
            </div>

            <!-- Cost Card -->
            <div class="card place-card">
                <div class="place-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.5 5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <h3 class="card-title">Стоимость</h3>
                </div>
                <div class="cost-details">
                    <div class="cost-row">
                        <span class="cost-label">Расчет по деталям:</span>
                        <span class="cost-value">4 636,8 ₽</span>
                    </div>
                    <div class="cost-row">
                        <span class="cost-label">Цена компонента:</span>
                        <span class="cost-value">{{ $place->price ?? '4 500 ₽' }}</span>
                    </div>
                    <div class="cost-row">
                        <span class="cost-label">Разница:</span>
                        <span class="cost-value cost-value-negative">+136,8 ₽</span>
                    </div>
                    <div class="cost-total">
                        <span class="cost-label">Итоговая цена:</span>
                        <span class="cost-total-value">{{ $place->price ?? '4 500 ₽' }}</span>
                    </div>
                    <p class="cost-per-unit">за 1 шт</p>
                    <div class="cost-total-stock">
                        <span class="cost-label">Общая стоимость запаса:</span>
                        <span class="cost-value">202 500 ₽</span>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card place-card">
                <div class="place-card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-6a1 1 0 011-1h2a1 1 0 011 1v15a1 1 0 01-1 1h-2a1 1 0 01-1-1V1z"/>
                    </svg>
                    <h3 class="card-title">Статистика</h3>
                </div>
                <div class="stats-details">
                    <div class="stat-row">
                        <span class="stat-label">Всего деталей:</span>
                        <span class="stat-value">49 шт</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Уникальных деталей:</span>
                        <span class="stat-value">3</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Конфигураций:</span>
                        <span class="stat-value">{{ $place->cabinets->count() ?? 0 }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Дата создания:</span>
                        <span class="stat-value">{{ $place->created_at->format('Y-m-d') ?? '2024-01-15' }}</span>
                    </div>
                </div>
            </div>

            <!-- Visualization Card -->
            <div class="card place-card">
                <h3 class="card-title">Визуализация</h3>
                <div class="visualization">
                    <div class="component-box component-base">
                        <span>ОСНОВАНИЕ</span>
                    </div>
                    <p class="component-label">Основание</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Ссылка на деталь */
    .detail-link {
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .detail-link:hover .detail-name {
        color: #3b82f6;
        font-weight: 600;
    }

    .detail-link:hover {
        opacity: 0.8;
    }

    /* Кликабельная строка таблицы */
    .detail-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .detail-row:hover {
        background-color: #f0f4f8 !important;
    }

    .detail-row:hover .detail-name {
        color: #3b82f6;
        font-weight: 600;
    }

    .detail-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-name {
        font-weight: 500;
        color: #101828;
        transition: color 0.2s ease;
    }

    .detail-code {
        font-size: 12px;
        color: #6a7282;
        font-family: 'Consolas', monospace;
    }
</style>

@endsection
