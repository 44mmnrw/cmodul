@php
$isEdit = !is_null($cabinet);
$productType = $productType ?? 1; // Дефолт на тип 1 (конфигурация)
$pageTitle = $isEdit ? 'Редактировать конфигурацию' : 'Добавить конфигурацию';
$isComponent = ($isEdit && $cabinet->product_type_id == 2) || (!$isEdit && $productType == 2);
$isConfiguration = (!$isEdit && $productType == 1) || ($isEdit && $cabinet->product_type_id == 1);
@endphp

<div class="config-edit-wrapper">
    <!-- Header Section -->
    <div class="config-edit-header">
        <button class="btn-back" onclick="history.back()" title="Вернуться назад">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="config-edit-header-content">
            <h1 class="config-edit-title">{{ $pageTitle }}</h1>
            <div class="config-edit-meta">
                <span class="config-code">{{ $isEdit ? $cabinet->scu : 'Новая конфигурация' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="config-edit-container">
        <form method="POST" action="{{ $action }}" class="config-edit-form">
            @csrf
            @if($isEdit)
                @method('PUT')
            @else
                <input type="hidden" name="type" value="{{ $productType }}">
            @endif

            <!-- Left Column -->
            <div class="config-edit-left">
                <!-- Basic Info Card -->
                <div class="card config-card">
                    <div class="config-card-header">
                        <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"/>
                        </svg>
                        <h3 class="card-title">Основная информация</h3>
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Название конфигурации</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            value="{{ old('name', $isEdit ? $cabinet->name : '') }}"
                            required
                        >
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="product_type_id" class="form-label">Тип</label>
                        <select 
                            id="product_type_id" 
                            name="product_type_id" 
                            class="form-control"
                            {{ $isEdit ? 'disabled' : '' }}
                            required
                        >
                            <option value="">Выберите тип...</option>
                            @foreach($productTypes as $pt)
                                <option value="{{ $pt->id }}" {{ (old('product_type_id', $isEdit ? $cabinet->product_type_id : $productType) == $pt->id) ? 'selected' : '' }}>
                                    {{ $pt->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_type_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Описание</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-control form-textarea" 
                            rows="4"
                        >{{ old('description', $isEdit ? $cabinet->description : '') }}</textarea>
                        @error('description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($isEdit)
                    <div class="form-group">
                        <label class="form-label">SCU (код)</label>
                        <div class="form-control-static">{{ $cabinet->scu }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="config-edit-right">
                <!-- Components Card (only for configurations, not for components) -->
                @if($isConfiguration)
                <div class="card config-card">
                    <div class="config-card-header">
                        <svg class="card-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                        </svg>
                        <h3 class="card-title">Компоненты конфигурации</h3>
                    </div>

                    <div class="components-edit-section">
                        <!-- Current Components -->
                        @if($isEdit)
                        <div class="current-components">
                            <h4 class="section-subtitle">Текущие компоненты</h4>
                            <div id="components-list" class="components-list">
                                @forelse($currentComponents as $index => $component)
                                    <div class="component-edit-item" data-component-id="{{ $component->id }}">
                                        <div class="component-edit-info">
                                            <div class="component-edit-name">{{ $component->name }}</div>
                                            <div class="component-edit-code">{{ $component->scu }}</div>
                                        </div>
                                        <div class="component-edit-quantity">
                                            <input 
                                                type="hidden" 
                                                name="components[{{ $index }}][id]" 
                                                value="{{ $component->id }}"
                                            >
                                            <input 
                                                type="number" 
                                                name="components[{{ $index }}][quantity]" 
                                                class="component-qty-input" 
                                                value="{{ $component->pivot->quantity }}"
                                                min="1"
                                                required
                                            >
                                            <span class="component-qty-label">шт</span>
                                        </div>
                                        <button type="button" class="btn-remove-component" onclick="this.closest('.component-edit-item').remove()">
                                            <svg viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <p>Компоненты не добавлены</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @else
                        <div id="components-list" class="components-list">
                            <div class="empty-state">
                                <p>Компоненты будут добавлены ниже</p>
                            </div>
                        </div>
                        @endif

                        <!-- Add Components -->
                        <div class="add-components">
                            <h4 class="section-subtitle">Добавить компоненты</h4>
                            <div class="add-component-form">
                                <select id="component-select" class="form-control">
                                    <option value="">Выберите компонент...</option>
                                    @foreach($availableComponents as $component)
                                        <option value="{{ $component->id }}" data-name="{{ $component->name }}" data-scu="{{ $component->scu }}">
                                            {{ $component->name }} ({{ $component->scu }})
                                        </option>
                                    @endforeach
                                </select>
                                <input 
                                    type="number" 
                                    id="component-qty" 
                                    class="form-control" 
                                    placeholder="Кол-во"
                                    min="1"
                                    value="1"
                                >
                                <button type="button" class="btn btn-primary" onclick="addComponent(); return false;">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
                                    </svg>
                                    Добавить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="config-edit-actions">
                    <a href="{{ $backRoute }}" class="btn btn-secondary">
                        Отмена
                    </a>
                    <button type="submit" class="btn btn-success">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                        {{ $isEdit ? 'Сохранить изменения' : 'Создать конфигурацию' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let componentCounter = 0;

    function addComponent() {
        console.log('addComponent called');
        
        const select = document.getElementById('component-select');
        const qty = document.getElementById('component-qty');
        const list = document.getElementById('components-list');
        
        if (!select || !list) {
            alert('Ошибка: элементы формы не найдены');
            return false;
        }
        
        if (!select.value) {
            alert('Выберите компонент');
            return false;
        }

        const option = select.options[select.selectedIndex];
        const componentId = select.value;
        const componentName = option.getAttribute('data-name');
        const componentScu = option.getAttribute('data-scu');
        const quantity = parseInt(qty.value) || 1;

        console.log('Adding:', {componentId, componentName, componentScu, quantity});
        
        // Проверим не добавлен ли уже этот компонент
        if (document.querySelector(`[data-component-id="${componentId}"]`)) {
            alert('Этот компонент уже добавлен');
            return false;
        }
        
        // Удаляем empty-state
        const emptyState = list.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        const item = document.createElement('div');
        item.className = 'component-edit-item';
        item.setAttribute('data-component-id', componentId);
        item.innerHTML = `
            <div class="component-edit-info">
                <div class="component-edit-name">${componentName}</div>
                <div class="component-edit-code">${componentScu}</div>
            </div>
            <div class="component-edit-quantity">
                <input 
                    type="hidden" 
                    name="components[${componentCounter}][id]" 
                    value="${componentId}"
                >
                <input 
                    type="number" 
                    name="components[${componentCounter}][quantity]" 
                    class="component-qty-input" 
                    value="${quantity}"
                    min="1"
                    required
                >
                <span class="component-qty-label">шт</span>
            </div>
            <button type="button" class="btn-remove-component" onclick="this.closest('.component-edit-item').remove(); return false;">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                </svg>
            </button>
        `;

        list.appendChild(item);
        componentCounter++;
        
        console.log('Component added, counter:', componentCounter);
        
        // Очищаем форму
        select.value = '';
        qty.value = '1';
        select.focus();
        
        return false;
    }

    // Инициализируем счётчик компонентов
    document.addEventListener('DOMContentLoaded', function() {
        const currentItemsCount = document.querySelectorAll('[data-component-id]').length;
        componentCounter = currentItemsCount;
        console.log('Form initialized with counter:', componentCounter);
    });
</script>
