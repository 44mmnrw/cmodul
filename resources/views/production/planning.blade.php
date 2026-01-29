@extends('layout')

@section('content')
<!-- Modal Components -->
<x-confirm-modal />
<x-alert-modal />

<div class="production-planning-container">
    <!-- Header -->
    <div class="planning-header">
        <div class="planning-header-left">
            <!-- Back Button -->
            <button onclick="history.back()" class="planning-header-back">
                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 10H5M5 10L10 15M5 10L10 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <!-- Title Section -->
            <div class="planning-header-title">
                <h1>Планирование производства</h1>
                <p>Расчет потребности и создание производственных заказов</p>
            </div>
        </div>
        
        <!-- Action Button -->
        <button class="planning-header-action" onclick="approvePlan()">
            <svg viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.5 3.5H9.5V9.5H3.5V10.5H9.5V16.5H10.5V10.5H16.5V9.5H10.5V3.5Z"/>
            </svg>
            Утвердить план и создать заказ
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="planning-stats-grid">
        <!-- Selected Orders Count -->
        <div class="stat-card">
            <div class="stat-card-content">
                <svg class="stat-card-icon icon-orders" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 4C4.89543 4 4 4.89543 4 6V26C4 27.1046 4.89543 28 6 28H26C27.1046 28 28 27.1046 28 26V10H14V4H6Z"/>
                </svg>
                <div class="stat-card-text">
                    <p class="stat-card-label">Выбрано заказов</p>
                    <p class="stat-card-value" id="selected-count">0</p>
                </div>
            </div>
        </div>

        <!-- Total Required -->
        <div class="stat-card">
            <div class="stat-card-content">
                <svg class="stat-card-icon icon-required" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2C8.26801 2 2 8.26801 2 16C2 23.732 8.26801 30 16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2Z"/>
                </svg>
                <div class="stat-card-text">
                    <p class="stat-card-label">Всего требуется</p>
                    <p class="stat-card-value"><span id="total-required">0</span> шт</p>
                </div>
            </div>
        </div>

        <!-- Can Assemble -->
        <div class="stat-card">
            <div class="stat-card-content">
                <svg class="stat-card-icon icon-assemble" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2C8.26801 2 2 8.26801 2 16C2 23.732 8.26801 30 16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2Z" fill="currentColor"/>
                    <path d="M13.5 20.5L9 16L10.41 14.59L13.5 17.67L21.59 9.59L23 11L13.5 20.5Z" fill="white"/>
                </svg>
                <div class="stat-card-text">
                    <p class="stat-card-label">Можно собрать</p>
                    <p class="stat-card-value"><span id="can-assemble">0</span> шт</p>
                </div>
            </div>
        </div>

        <!-- Need to Produce -->
        <div class="stat-card">
            <div class="stat-card-content">
                <svg class="stat-card-icon icon-produce" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2C8.26801 2 2 8.26801 2 16C2 23.732 8.26801 30 16 30C23.732 30 30 23.732 30 16C30 8.26801 23.732 2 16 2Z"/>
                </svg>
                <div class="stat-card-text">
                    <p class="stat-card-label">Нужно произвести</p>
                    <p class="stat-card-value"><span id="need-produce">0</span> шт</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="planning-table-container">
        <div class="planning-table-header">
            <h3 class="planning-table-title">Заказы клиентов</h3>
            <p class="planning-table-subtitle">Выберите заказы для планирования производства</p>
        </div>

        <div style="overflow-x: auto;">
            <table class="planning-table">
                <thead>
                    <tr>
                        <th style="width: 61px;">
                            <input type="checkbox" id="select-all" onchange="toggleAllOrders()">
                        </th>
                        <th>Номер заказа</th>
                        <th>Дата заказа</th>
                        <th>Срок поставки</th>
                        <th class="text-center">Позиций</th>
                        <th class="text-center">Количество</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    @forelse($orders as $groupIndex => $group)
                        <tr class="order-row" data-order-id="{{ $group['order_id'] }}" style="cursor: pointer;">
                            <td>
                                <input type="checkbox" class="order-checkbox" value="{{ $group['order_id'] }}" data-order-nums="{{ json_encode($group['order_items']->pluck('id')->toArray()) }}" onchange="updateAnalysis()">
                            </td>
                            <td>{{ $group['order_items']->first()?->order->order_num ?? 'N/A' }}</td>
                            <td>{{ $group['created_at']?->format('d.m.Y') ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $group['planned_date']?->format('d.m.Y') ?? 'N/A' }}</div>
                                <div class="date-warning">через -348 дн.</div>
                            </td>
                            <td class="text-center">{{ $group['positions_count'] }}</td>
                            <td class="text-center">
                                <span class="qty-badge blue">{{ $group['total_quantity'] }} шт</span>
                            </td>
                        </tr>
                        <tr class="details-row hidden" id="details-{{ $groupIndex }}">
                            <td colspan="6">
                                <div class="details-content">
                                    <div class="details-label">Позиции в заказе {{ $group['order_items']->first()?->order->order_num ?? 'N/A' }}</div>
                                    <div class="details-components">
                                        @forelse($group['order_items'] as $order)
                                            <div class="component-item">
                                                <div class="component-item-left">
                                                    <p class="component-item-name">{{ $order->product->name ?? 'N/A' }}</p>
                                                    <p class="component-item-required">
                                                        @if($order->product->product_type_id == 1)
                                                            Конфигурация ({{ $order->product->scu ?? 'N/A' }})
                                                        @else
                                                            Компонент ({{ $order->product->scu ?? 'N/A' }})
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="component-item-right">
                                                    @if($order->product->product_type_id == 1)
                                                        @php
                                                            $virtualStock = $order->product->getVirtualStock();
                                                        @endphp
                                                        <p class="component-item-stock">На складе: {{ $virtualStock['quantity'] ?? 0 }} шт (виртуально)</p>
                                                        @if(isset($virtualStock['limiting_component']))
                                                            <p class="component-item-can-assemble">Лимит: {{ $virtualStock['limiting_component']->name }}</p>
                                                        @endif
                                                    @else
                                                        <p class="component-item-stock">На складе: {{ $order->product->stock->available ?? 0 }} шт</p>
                                                        <p class="component-item-can-assemble">В резерве: {{ $order->product->stock->reserved ?? 0 }} шт</p>
                                                    @endif
                                                    <p style="margin-top: 8px; font-weight: 600; color: #2563eb;">Требуется: {{ $order->quantity_ordered }} шт</p>
                                                </div>
                                            </div>
                                            @if($order->product->product_type_id == 1)
                                                <div style="margin-top: 16px; padding: 12px; background: #e0f2fe; border-left: 3px solid #0284c7; border-radius: 4px; font-size: 13px; color: #0c4a6e;">
                                                    <strong>Компоненты в составе конфигурации:</strong>
                                                    <ul style="margin: 8px 0 0 20px; list-style: disc;">
                                                        @forelse($order->product->componentsInConfiguration as $comp)
                                                            <li>{{ $comp->name }} × {{ $comp->pivot->quantity }} (доступно: {{ $comp->stock->available ?? 0 }})</li>
                                                        @empty
                                                            <li>Компоненты не найдены</li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            @endif
                                            @if($order->notes)
                                                <div style="margin-top: 12px; padding: 12px; background: #f3f4f6; border-radius: 8px; font-size: 14px; color: #4a5565;">
                                                    <strong>Примечания:</strong> {{ $order->notes }}
                                                </div>
                                            @endif
                                        @empty
                                            <p style="color: #4a5565; font-size: 14px;">Позиции не найдены</p>
                                        @endforelse
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">Нет утвежденных производственных заказов</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Configuration Requirements Table -->
    <div class="planning-table-container">
        <div class="planning-table-header">
            <h3 class="planning-table-title">Потребность в компонентах</h3>
            <p class="planning-table-subtitle">Расчет необходимых компонентов для выбранных заказов</p>
        </div>

        <div style="overflow-x: auto;">
            <table class="planning-table">
                <thead>
                    <tr>
                        <th>Компонент</th>
                        <th class="text-center">Требуется</th>
                        <th class="text-center">На складе</th>
                        <th class="text-center">Нужно произвести</th>
                    </tr>
                </thead>
                <tbody id="requirements-tbody">
                    <tr>
                        <td colspan="4" class="empty-state">Выберите заказы для расчета потребности</td>
                    </tr>
                </tbody>
                <tfoot id="requirements-tfoot" style="display: none;">
                    <tr>
                        <td class="text-right" style="font-weight: 600;">Итого:</td>
                        <td class="text-center"><span id="total-qty">0</span> шт</td>
                        <td class="text-center"><span id="total-in-stock">0</span> шт</td>
                        <td class="text-center"><span id="total-need-produce-footer">0</span> шт</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    setupOrderRows();
});

// Раскрытие деталей при клике на строку
function setupOrderRows() {
    const rows = document.querySelectorAll('.order-row');
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox') return;
            const orderId = this.dataset.orderId;
            const detailsRow = document.getElementById(`details-${orderId}`);
            if (detailsRow) {
                detailsRow.classList.toggle('hidden');
            }
        });
    });
}

// Выбрать все заказы
function toggleAllOrders() {
    const selectAllCheckbox = document.getElementById('select-all');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    orderCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateAnalysis();
}

// Глобальная переменная для хранения результатов анализа
let lastAnalysisComponents = [];

// Обновить анализ при изменении выбора
async function updateAnalysis() {
    const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
    const orderIds = [];
    
    // Собрать все ID позиций из выбранных групп
    selectedCheckboxes.forEach(cb => {
        const orderNums = JSON.parse(cb.dataset.orderNums);
        orderIds.push(...orderNums);
    });

    // Обновить счетчик выбранных
    document.getElementById('selected-count').textContent = selectedCheckboxes.length;

    if (orderIds.length === 0) {
        // Очистить данные если ничего не выбрано
        document.getElementById('total-required').textContent = '0';
        document.getElementById('can-assemble').textContent = '0';
        document.getElementById('need-produce').textContent = '0';
        document.getElementById('requirements-tbody').innerHTML = '<tr><td colspan="4" class="empty-state">Выберите заказы для расчета потребности</td></tr>';
        document.getElementById('requirements-tfoot').style.display = 'none';
        lastAnalysisComponents = [];
        return;
    }

    // Получить анализ с сервера
    try {
        const response = await fetch('{{ route("production-planning.analyze") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_ids: orderIds })
        });

        const data = await response.json();

        // Сохранить компоненты для использования в approvePlan
        lastAnalysisComponents = data.components;

        // Обновить счетчики
        document.getElementById('total-required').textContent = data.total_required;
        document.getElementById('can-assemble').textContent = data.can_assemble;
        document.getElementById('need-produce').textContent = data.need_to_produce;

        // Обновить таблицу потребности
        updateRequirementsTable(data.components);
        
    } catch (error) {
        console.error('Error:', error);
    }
}

// Обновить таблицу потребности в компонентах
function updateRequirementsTable(components) {
    const tbody = document.getElementById('requirements-tbody');
    const tfoot = document.getElementById('requirements-tfoot');

    if (components.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Нет данных для отображения</td></tr>';
        tfoot.style.display = 'none';
        return;
    }

    let totalRequired = 0;
    let totalInStock = 0;
    let totalNeedProduce = 0;

    tbody.innerHTML = components.map(comp => {
        totalRequired += comp.required_total;
        totalInStock += comp.in_stock;
        totalNeedProduce += comp.need_to_produce;

        const badgeClass = comp.need_to_produce > 0 ? 'orange' : 'green';

        return `
            <tr>
                <td>
                    <p style="margin: 0; font-weight: 500; color: #101828;">${comp.component_name}</p>
                    <p style="margin: 0; font-size: 12px; font-family: Consolas, monospace; color: #6a7282;">${comp.component_scu}</p>
                </td>
                <td class="text-center">
                    <span class="qty-badge gray">${comp.required_total} шт</span>
                </td>
                <td class="text-center">
                    <span class="qty-badge blue">${comp.in_stock} шт</span>
                </td>
                <td class="text-center">
                    <span class="qty-badge ${badgeClass}">${comp.need_to_produce} шт</span>
                </td>
            </tr>
        `;
    }).join('');

    // Обновить footer
    document.getElementById('total-qty').textContent = totalRequired;
    document.getElementById('total-in-stock').textContent = totalInStock;
    document.getElementById('total-need-produce-footer').textContent = totalNeedProduce;
    tfoot.style.display = 'table-footer-group';
}

// Утвердить план и создать заказ
async function approvePlan() {
    console.log('=== APPROVE PLAN CALLED ===');
    
    // Получить выбранные заказы
    const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
    console.log('Selected checkboxes:', selectedCheckboxes.length);
    
    if (selectedCheckboxes.length === 0) {
        showAlert('Ошибка', 'Пожалуйста, выберите хотя бы один заказ для утверждения');
        return;
    }

    // Собрать ВСЕ ID позиций (production_orders) из выбранных групп
    const orderIds = [];
    selectedCheckboxes.forEach(cb => {
        const positionIds = JSON.parse(cb.dataset.orderNums);
        orderIds.push(...positionIds);
    });

    console.log('Order IDs collected:', orderIds);
    console.log('Last analysis components:', lastAnalysisComponents);

    // Проверить, есть ли компоненты для производства
    if (lastAnalysisComponents.length === 0) {
        showAlert('Ошибка', 'Сначала выберите заказы и дождитесь расчета потребности');
        return;
    }

    // Подсчитать, сколько компонентов нужно произвести
    let needToProduce = 0;
    lastAnalysisComponents.forEach(comp => {
        if (comp.need_to_produce > 0) {
            needToProduce++;
        }
    });

    console.log('Need to produce:', needToProduce);

    if (needToProduce === 0) {
        showAlert('Информация', 'Все необходимые компоненты уже есть на складе. Нечего производить.');
        return;
    }

    // Правильное склонение слова "компонент"
    let componentText = '';
    if (needToProduce === 1) {
        componentText = '1 компонент';
    } else if (needToProduce >= 2 && needToProduce <= 4) {
        componentText = `${needToProduce} компонента`;
    } else {
        componentText = `${needToProduce} компонентов`;
    }

    console.log('Component text:', componentText);

    // Показать подтверждение через модальное окно
    const confirmMessage = `Вы уверены, что хотите утвердить план и создать заказ?`;
    const confirmSubtext = `Будут созданы производственные позиции для: ${componentText}. Статус исходного заказа изменится на "В производстве".`;
    
    console.log('Opening confirm modal');
    
    openConfirmModal(
        'Утвердить план',
        confirmMessage,
        confirmSubtext,
        'Утвердить',
        async () => {
            console.log('Confirm modal confirmed, submitting plan...');
            await submitApprovePlan(orderIds, lastAnalysisComponents);
        }
    );
}

async function submitApprovePlan(orderIds, components) {
    try {
        console.log('=== APPROVE PLAN START ===');
        console.log('Order IDs:', orderIds);
        console.log('Components:', components);
        
        const payload = { 
            order_ids: orderIds,
            components: components
        };
        
        console.log('Full payload:', JSON.stringify(payload));
        console.log('Route:', '{{ route("production-planning.approve") }}');

        // Отправить запрос на сервер
        const response = await fetch('{{ route("production-planning.approve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            showAlert(
                '✅ Успех',
                data.message,
                () => {
                    // Перенаправить на страницу производственных заказов
                    window.location.href = data.redirect;
                }
            );
        } else {
            showAlert('❌ Ошибка', `Ошибка: ${data.message}`);
        }
        
    } catch (error) {
        console.error('=== APPROVE PLAN ERROR ===');
        console.error('Error:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        showAlert('❌ Ошибка', 'Ошибка при создании заказа. Проверьте консоль браузера.');
    }
}
</script>
@endsection
