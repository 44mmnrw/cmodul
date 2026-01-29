

<?php $__env->startSection('title', $orders ? 'Редактировать производственный заказ' : 'Новый производственный заказ'); ?>

<?php $__env->startSection('content'); ?>
<div class="order-form-container">
    <div class="order-form-card">
        <!-- Header -->
        <div class="form-header">
            <h3 class="form-heading"><?php echo e($orders ? 'Редактировать заказ' : 'Форма производственного заказа'); ?></h3>
            <p class="form-subheading"><?php echo e($orders ? $orders->first()?->order->order_num . ' · ' . $orders->first()?->created_at->format('d.m.Y') : 'Заполните данные заказчика и список производимых шкафов'); ?></p>
        </div>

        <form action="<?php echo e(route('production-orders.store')); ?>" method="POST" class="order-form">
            <?php echo csrf_field(); ?>
            <?php if($orders): ?>
                <input type="hidden" name="edit_id" value="<?php echo e($orders->first()?->id); ?>">
            <?php endif; ?>

            <!-- Dates Row -->
            <div class="form-dates-row">
                <div class="form-group-col">
                    <label class="form-label">
                        Дата заказа
                        <span class="required">*</span>
                    </label>
                    <div class="date-input-wrapper">
                        <svg class="date-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M3 2V4M13 2V4M2 7H14M2 6H14C13.4477 6 13 6.44772 13 7V13C13 13.5523 13.4477 14 14 14H2C1.44772 14 1 13.5523 1 13V7C1 6.44772 1.44772 6 2 6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input type="date" name="order_date" class="form-control date-input" value="<?php echo e($orders && $orders->first()?->order?->order_date ? $orders->first()->order->order_date->format('Y-m-d') : date('Y-m-d')); ?>" required>
                    </div>
                    <?php $__errorArgs = ['order_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="form-error"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- Configurations Table -->
            <div class="form-group-table">
                <label class="form-label">
                    Конфигурации к производству
                    <span class="required">*</span>
                </label>

                <div class="table-wrapper">
                    <table class="configs-table">
                        <thead>
                            <tr>
                                <th class="col-number">№</th>
                                <th class="col-config">Конфигурация <span class="required">*</span></th>
                                <th class="col-quantity">Кол-во <span class="required">*</span></th>
                                <th class="col-date">Плановая дата <span class="required">*</span></th>
                                <th class="col-actions"></th>
                            </tr>
                        </thead>
                        <tbody id="configsTableBody">
                            <?php if($orders): ?>
                                <?php $rowNum = 0; ?>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $rowNum++; ?>
                                    <tr class="config-row" data-row="<?php echo e($rowNum); ?>">
                                        <td class="col-number"><span class="row-number"><?php echo e($rowNum); ?></span></td>
                                        <td class="col-config">
                                            <select name="configs[<?php echo e($rowNum); ?>][product_id]" class="config-select form-control">
                                                <option value="">Выберите конфигурацию...</option>
                                                <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($component->id); ?>" <?php echo e((int)$component->id === (int)$order->product_id ? 'selected' : ''); ?>><?php echo e($component->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td class="col-quantity">
                                            <input type="number" name="configs[<?php echo e($rowNum); ?>][quantity]" class="config-qty form-control" min="1" placeholder="0" value="<?php echo e($order->quantity_ordered); ?>">
                                        </td>
                                        <td class="col-date">
                                            <input type="date" name="configs[<?php echo e($rowNum); ?>][planned_date]" class="form-control" required value="<?php echo e($order->planned_date ? $order->planned_date->format('Y-m-d') : ''); ?>">
                                        </td>
                                        <td class="col-actions">
                                            <button type="button" class="btn-delete" title="Удалить строку">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M2 4H14M6.5 7V12M9.5 7V12M3 4L4 13C4 13.5523 4.44772 14 5 14H11C11.5523 14 12 13.5523 12 13L13 4M6 4V3C6 2.44772 6.44772 2 7 2H9C9.55228 2 10 2.44772 10 3V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr class="config-row" data-row="1">
                                    <td class="col-number"><span class="row-number">1</span></td>
                                    <td class="col-config">
                                        <select name="configs[1][product_id]" class="config-select form-control">
                                            <option value="">Выберите конфигурацию...</option>
                                            <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($component->id); ?>"><?php echo e($component->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                    <td class="col-quantity">
                                        <input type="number" name="configs[1][quantity]" class="config-qty form-control" min="1" placeholder="0">
                                    </td>
                                    <td class="col-date">
                                        <input type="date" name="configs[1][planned_date]" class="form-control" required>
                                    </td>
                                    <td class="col-actions">
                                        <button type="button" class="btn-delete" title="Удалить строку">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4H14M6.5 7V12M9.5 7V12M3 4L4 13C4 13.5523 4.44772 14 5 14H11C11.5523 14 12 13.5523 12 13L13 4M6 4V3C6 2.44772 6.44772 2 7 2H9C9.55228 2 10 2.44772 10 3V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="table-footer">
                        <button type="button" class="btn-add-row" id="addRowBtn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Добавить строку
                        </button>
                        <div class="row-counter">
                            <span id="filledRows">0</span> из <span id="totalRows">1</span>
                        </div>
                    </div>
                </div>

                <?php $__errorArgs = ['configs'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="form-error"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Status Selection -->
            <div class="form-group-col">
                <label class="form-label">Статус заказа</label>
                <select name="status_id" class="form-control form-select">
                    <option value="">Выберите статус...</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status->id); ?>" <?php echo e($orders && $orders->first()?->status_id === $status->id ? 'selected' : ''); ?>>
                            <?php echo e($status->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="form-error"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Notes -->
            <div class="form-group-col">
                <label class="form-label">Примечания</label>
                <textarea name="notes" class="form-control form-textarea" placeholder="Дополнительная информация о заказе..."><?php echo e(old('notes', $orders ? $orders->first()?->notes : '')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="form-error"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="reset" class="btn btn-outline">Очистить</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M2 14V8M8 2L14 8V14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo e($orders ? 'Обновить заказ' : 'Разместить заказ'); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowCounter = 1;

    function updateRowNumbers() {
        document.querySelectorAll('.config-row').forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
            row.dataset.row = index + 1;
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.name.replace(/\[\d+\]/, '[' + (index + 1) + ']');
                input.name = name;
            });
        });
        updateCounters();
    }

    function updateCounters() {
        const totalRows = document.querySelectorAll('.config-row').length;
        const filledRows = document.querySelectorAll('.config-row').filter(row => {
            return row.querySelector('.config-select').value && row.querySelector('.config-qty').value;
        }).length;
        
        document.getElementById('totalRows').textContent = totalRows;
        document.getElementById('filledRows').textContent = filledRows;
    }

    document.getElementById('addRowBtn').addEventListener('click', function(e) {
        e.preventDefault();
        rowCounter++;
        const tbody = document.getElementById('configsTableBody');
        const newRow = document.createElement('tr');
        newRow.className = 'config-row';
        newRow.dataset.row = rowCounter;
        
        newRow.innerHTML = `
            <td class="col-number"><span class="row-number">${rowCounter}</span></td>
            <td class="col-config">
                <select name="configs[${rowCounter}][product_id]" class="config-select form-control">
                    <option value="">Выберите конфигурацию...</option>
                    <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($component->id); ?>"><?php echo e($component->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
            <td class="col-quantity">
                <input type="number" name="configs[${rowCounter}][quantity]" class="config-qty form-control" min="1" placeholder="0">
            </td>
            <td class="col-date">
                <input type="date" name="configs[${rowCounter}][planned_date]" class="form-control" required>
            </td>
            <td class="col-actions">
                <button type="button" class="btn-delete" title="Удалить строку">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M2 4H14M6.5 7V12M9.5 7V12M3 4L4 13C4 13.5523 4.44772 14 5 14H11C11.5523 14 12 13.5523 12 13L13 4M6 4V3C6 2.44772 6.44772 2 7 2H9C9.55228 2 10 2.44772 10 3V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </td>
        `;
        
        tbody.appendChild(newRow);
        updateRowNumbers();
        attachRowEvents(newRow);
    });

    function attachRowEvents(row) {
        row.querySelector('.btn-delete').addEventListener('click', function(e) {
            e.preventDefault();
            if (document.querySelectorAll('.config-row').length > 1) {
                row.remove();
                updateRowNumbers();
            }
        });

        row.querySelectorAll('.config-select, .config-qty').forEach(input => {
            input.addEventListener('change', updateCounters);
            input.addEventListener('input', updateCounters);
        });
    }

    document.querySelectorAll('.config-row').forEach(attachRowEvents);
    updateCounters();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/production-orders/create.blade.php ENDPATH**/ ?>