

<?php $__env->startSection('content'); ?>
<div class="shipments-wrapper">
    <!-- Header Section -->
    <div class="shipments-header">
        <div class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </div>
        <div class="header-content">
            <div class="header-icon-box blue">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 5h6v2H9V5m0 4h6v2H9V9M7 2h10c1.1 0 2 .9 2 2v16c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2m0 2v16h10V4H7z"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>Отгрузка шкафов</h1>
                <p>Регистрация отгрузки готовых шкафов клиентам</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-header">
            <h3>Форма регистрации отгрузки</h3>
            <p>Заполните данные клиента и список отгружаемых шкафов</p>
        </div>

        <form class="form-content" method="POST" action="<?php echo e(route('shipments.store')); ?>">
            <?php echo csrf_field(); ?>

            <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
            <?php endif; ?>

            <!-- Client and Date Row -->
            <div class="form-row">
                <div class="form-group">
                    <label for="client">Клиент <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <input 
                            type="text" 
                            id="client" 
                            name="client" 
                            class="form-input"
                            placeholder="ООО &quot;Компания&quot; или ИП Иванов И.И."
                            required
                        >
                    </div>
                    <?php $__errorArgs = ['client'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="shipment_date">Дата отгрузки <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                        </svg>
                        <input 
                            type="date" 
                            id="shipment_date" 
                            name="shipment_date" 
                            class="form-input"
                            required
                        >
                    </div>
                    <?php $__errorArgs = ['shipment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error-message"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="form-group">
                <label for="address">Адрес доставки</label>
                <div class="input-with-icon">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5z"/>
                    </svg>
                    <input 
                        type="text" 
                        id="address" 
                        name="address" 
                        class="form-input"
                        placeholder="г. Москва, ул. Примерная, д. 1"
                    >
                </div>
                <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="error-message"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Shipment Items Table -->
            <div class="form-group">
                <label>Отгружаемые шкафы <span class="required">*</span></label>
                <div class="table-wrapper">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="col-config">Конфигурация шкафа <span class="required">*</span></th>
                                <th class="col-quantity">Количество <span class="required">*</span></th>
                                <th class="col-price">Цена за шт.</th>
                                <th class="col-total">Сумма</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>
                        <tbody id="items-table-body">
                            <tr class="item-row" data-row-id="0">
                                <td class="col-config">
                                    <select name="items[0][config_id]" class="config-select" required>
                                        <option value="">Выберите конфигурацию...</option>
                                        <?php $__currentLoopData = $configurations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($config['id']); ?>">
                                            <?php echo e($config['name']); ?> (остаток: <?php echo e($config['virtual_stock'] ?? 0); ?> шт)
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td class="col-quantity">
                                    <input 
                                        type="number" 
                                        name="items[0][quantity]" 
                                        class="quantity-input" 
                                        min="0" 
                                        value="0"
                                        required
                                    >
                                </td>
                                <td class="col-price">
                                    <span class="price-display">0 ₽</span>
                                </td>
                                <td class="col-total">
                                    <span class="total-display">0 ₽</span>
                                </td>
                                <td class="col-action">
                                    <button type="button" class="delete-row-btn" onclick="deleteRow(this)" disabled>
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3" class="total-label">Итого:</td>
                                <td class="total-amount"><strong id="total-sum">0 ₽</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="error-message"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Add Row Button -->
            <div class="add-row-section">
                <button type="button" class="add-row-btn" onclick="addTableRow()">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Добавить строку
                </button>
                <span class="row-counter">Заполнено строк: <span id="filled-rows">0</span> из <span id="total-rows">1</span></span>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label for="notes">Примечания</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    class="form-textarea"
                    placeholder="Дополнительная информация об отгрузке..."
                    rows="5"
                ></textarea>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="reset" class="btn-clear">Очистить</button>
                <button type="submit" class="btn-save">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                    </svg>
                    Сохранить отгрузку
                </button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/shipments-form.css')); ?>">

<script>
// Table row management
let rowCounter = 1;

function addTableRow() {
    const tbody = document.getElementById('items-table-body');
    const newRow = tbody.querySelector('.item-row').cloneNode(true);
    
    // Update row ID and input names
    newRow.dataset.rowId = rowCounter;
    newRow.querySelectorAll('input, select').forEach(el => {
        const name = el.name.replace(/\[\d+\]/, `[${rowCounter}]`);
        el.name = name;
        el.value = el.type === 'number' ? 0 : '';
    });
    
    // Enable delete button
    newRow.querySelector('.delete-row-btn').disabled = false;
    
    tbody.appendChild(newRow);
    rowCounter++;
    updateTotals();
}

function deleteRow(btn) {
    const row = btn.closest('.item-row');
    row.remove();
    updateTotals();
}

function updateTotals() {
    let filledRows = 0;
    let totalSum = 0;
    const totalRowsEl = document.getElementById('total-rows');
    
    document.querySelectorAll('.item-row').forEach((row, index) => {
        const qtyInput = row.querySelector('.quantity-input');
        const qty = parseInt(qtyInput.value) || 0;
        
        if (qty > 0) {
            filledRows++;
        }
        
        // Calculate row total (simplified, would need actual price data)
        const rowTotal = qty * 500000; // Placeholder price
        totalSum += rowTotal;
    });
    
    document.getElementById('filled-rows').textContent = filledRows;
    document.getElementById('total-rows').textContent = document.querySelectorAll('.item-row').length;
    document.getElementById('total-sum').textContent = formatPrice(totalSum);
}

function formatPrice(value) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0,
    }).format(value);
}

// Event listeners
document.getElementById('items-table-body').addEventListener('change', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        updateTotals();
    }
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/shipments/form.blade.php ENDPATH**/ ?>