@extends('layout')

@section('title', $detail->name . ' - Детали')

@section('content')
<div class="detail-page">
    <!-- Header Section with Back Button -->
    <div class="page-header">
        <div class="header-back-button">
            <a href="{{ route('details.index') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5">
                    <path fill-rule="evenodd" d="M12 19l-8-8 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <div class="header-content">
            <div class="header-title-row">
                <h1 class="header-title">{{ $detail->name }}</h1>
                @if($detail->category)
                    <span class="category-badge">{{ $detail->category->name }}</span>
                @endif
            </div>
            <p class="header-code">{{ $detail->scu ?? 'PROD-001' }}</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="header-actions">
            <a href="{{ route('details.edit', $detail) }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                Редактировать
            </a>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="main-grid">
        <!-- Left Column -->
        <div class="left-column">
            
            <!-- Source Card -->
            <div class="card">
                <div class="card-body">
                    <div class="source-section">
                        <div class="source-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="source-content">
                            <h3 class="section-title">Источник</h3>
                            <div class="source-badge">
                                <svg class="badge-icon" viewBox="0 0 16 16" fill="currentColor">
                                    <circle cx="8" cy="8" r="7"/>
                                </svg>
                                <span>{{ $detail->source->name ?? 'Не указан' }}</span>
                            </div>
                            <p class="source-description">
                                @if($detail->source && $detail->source->name === 'Покупка')
                                    Деталь приобретается у поставщиков. Зависит от наличия на складе поставщика и сроков доставки.
                                @else
                                    Деталь производится на собственных мощностях. Полный контроль над производственным процессом и сроками изготовления.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Characteristics Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                    </svg>
                    <h3 class="section-title">Характеристики</h3>
                </div>
                <div class="characteristics-grid">
                    <div class="char-item">
                        <span class="char-label">Размеры</span>
                        <span class="char-value">{{ $detail->width }}x{{ $detail->height }}x{{ $detail->depth }}мм</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Материал</span>
                        <span class="char-value">{{ $detail->material ?? 'Сталь холоднокатаная' }}</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Вес</span>
                        <span class="char-value">{{ $detail->weight }} кг</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Единица измерения</span>
                        <span class="char-value">{{ $detail->scu ?? 'шт' }}</span>
                    </div>
                    <div class="char-item">
                        <span class="char-label">Категория</span>
                        <span class="char-value">{{ $detail->category->name ?? 'Общее' }}</span>
                    </div>
                </div>
            </div>

            <!-- Components Table Card -->
            <div class="card">
                <div class="card-header-with-count">
                    <div class="header-left">
                        <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 000-2H7zM7 7a1 1 0 000 2h6a1 1 0 000-2H7zM7 11a1 1 0 100 2h6a1 1 0 100-2H7zM3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 7.324a2 2 0 01-1.952 2.41H6.54a2 2 0 00-1.952 2.41l.833 4.89A2 2 0 018.41 22h5.18a2 2 0 001.946-1.57l1-5.9"/>
                        </svg>
                        <h3 class="section-title">Используется в компонентах</h3>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="components-table">
                        <thead>
                            <tr>
                                <th>ТИП</th>
                                <th>КОМПОНЕНТ</th>
                                <th class="center">КОЛ-ВО НА 1 ШТ</th>
                                <th class="center">ОСТАТОК КОМПОНЕНТОВ</th>
                                <th class="center">ВСЕГО ТРЕБУЕТСЯ</th>
                                <th class="right">ЦЕНА КОМПОНЕНТА</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detail->usedInCabinets as $cabinet)
                                <tr class="place-row" onclick="window.location.href = '{{ route('details.show', $cabinet) }}';" style="cursor: pointer;">
                                    <td>
                                        <span class="type-badge">Боковая панель</span>
                                    </td>
                                    <td>
                                        <div class="component-info">
                                            <div class="component-name">{{ $cabinet->name }}</div>
                                            <span class="component-code">{{ $cabinet->scu }}</span>
                                        </div>
                                    </td>
                                    <td class="center">{{ $cabinet->pivot->quantity }} шт</td>
                                    <td class="center">120 шт</td>
                                    <td class="center">480 шт</td>
                                    <td class="right">{{ number_format($cabinet->price ?? 0, 0, ',', ' ') }} ₽</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Деталь не используется в компонентах
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock Analysis Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="section-title">Анализ остатков</h3>
                </div>

                <div class="stock-analysis-grid">
                    <div class="stock-metric">
                        <span class="metric-label">Физический остаток</span>
                        <span class="metric-value">210</span>
                        <span class="metric-unit">шт</span>
                    </div>
                    <div class="stock-metric reserved">
                        <span class="metric-label">Зарезервировано</span>
                        <span class="metric-value">735</span>
                        <span class="metric-unit">шт</span>
                    </div>
                    <div class="stock-metric available">
                        <span class="metric-label">Доступно</span>
                        <span class="metric-value">-525</span>
                        <span class="metric-unit">шт</span>
                    </div>
                </div>

                <!-- Critical Alert -->
                <div class="alert-critical">
                    <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="alert-content">
                        <h4 class="alert-title">Критический дефицит!</h4>
                        <p class="alert-text">Текущего запаса недостаточно для обеспечения всех компонентов на складе. Необходимо пополнение на 525 шт.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            
            <!-- Stock Summary Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 12a9 9 0 1 0 18 0A9 9 0 0 0 3 12Z"/>
                    </svg>
                    <h3 class="section-title">Остатки</h3>
                </div>

                <div class="stock-item">
                    <span class="stock-label">Текущий остаток</span>
                    <div class="stock-value-display">
                        <span class="value-number">210</span>
                        <span class="value-unit">шт</span>
                    </div>
                </div>

                <div class="stock-item divider">
                    <span class="stock-label">Общая стоимость запаса</span>
                    <span class="stock-price-value">88 200 ₽</span>
                </div>
            </div>

            <!-- Cost Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.5 11a1 1 0 11-2 0 1 1 0 012 0zM12.5 11a1 1 0 11-2 0 1 1 0 012 0zM5 8a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                    <h3 class="section-title">Стоимость</h3>
                </div>

                <div class="cost-list">
                    <div class="cost-row">
                        <span class="cost-label">Цена за шт:</span>
                        <span class="cost-value">{{ number_format($detail->price ?? 420, 0, ',', ' ') }} ₽</span>
                    </div>
                    <div class="cost-row divider">
                        <span class="cost-label">Цена за кг:</span>
                        <span class="cost-value">{{ number_format($detail->price_per_kg ?? 350, 2, ',', ' ') }} ₽</span>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="section-title">Статистика</h3>
                </div>

                <div class="stats-list">
                    <div class="stat-row">
                        <span class="stat-label">Компонентов:</span>
                        <span class="stat-value">{{ $detail->componentsInConfiguration->count() }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Зарезервировано:</span>
                        <span class="stat-value">735 шт</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Дата создания:</span>
                        <span class="stat-value">{{ $detail->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Источник:</span>
                        <span class="stat-value">Свое пр-во</span>
                    </div>
                </div>
            </div>

            <!-- Visualization Card -->
            <div class="card">
                <h3 class="section-title">Визуализация</h3>
                <div class="visualization-container">
                    <div class="visualization-item">
                        <div class="visualization-box">
                            <svg class="visualization-icon" viewBox="0 0 64 64" fill="currentColor">
                                <path d="M32 8L8 20v24c0 13.25 24 20 24 20s24-6.75 24-20V20L32 8z" opacity="0.2"/>
                                <path d="M32 12L12 22v20c0 10 20 16 20 16s20-6 20-16V22L32 12z" fill="none" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="vis-label">{{ $detail->category->name ?? 'Металл' }}</span>
                        <span class="vis-unit">{{ $detail->scu ?? 'шт' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
