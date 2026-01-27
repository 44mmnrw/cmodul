@extends('layout')

@section('content')
<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="title-section">
                <h1 class="page-title">Управление компонентами</h1>
                <p class="page-subtitle">Всего компонентов: {{ count($places) }}</p>
            </div>
            <button class="btn-primary btn-lg">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Добавить компонент
            </button>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card search-filter-card">
        <div class="search-filter-grid">
            <div class="search-input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input 
                    type="text" 
                    class="search-input" 
                    placeholder="Поиск по названию или коду..."
                >
            </div>
            <div class="filter-dropdown-wrapper">
                <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <line x1="4" y1="6" x2="20" y2="6"></line>
                    <line x1="4" y1="12" x2="20" y2="12"></line>
                    <line x1="4" y1="18" x2="20" y2="18"></line>
                </svg>
                <select class="filter-dropdown">
                    <option>Все типы</option>
                    <option>Основание</option>
                    <option>Боковая панель</option>
                    <option>Дверь</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card table-card">
        <div class="table-wrapper">
            <table class="components-table">
                <thead>
                    <tr>
                        <th>Тип</th>
                        <th>Код</th>
                        <th>Название</th>
                        <th>Характеристики</th>
                        <th class="text-right">Цена</th>
                        <th class="text-right">Остаток</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($places as $place)
                    <tr style="cursor: pointer;" onclick="window.location.href='/places/{{ $place->place_id }}'">
                        <td>
                            <span class="badge badge-blue">Место</span>
                        </td>
                        <td class="code">{{ $place->place_id }}</td>
                        <td>{{ $place->name }}</td>
                        <td class="specs">
                            <div class="spec-line">-</div>
                        </td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="actions" onclick="event.stopPropagation();">
                            <button class="action-btn edit-btn" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94m-6.87-6.87l2.92-2.92a2.121 2.121 0 0 1 3 3l-2.92 2.92"></path>
                                </svg>
                            </button>
                            <button class="action-btn delete-btn" title="Удалить">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">
                            Нет данных
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
