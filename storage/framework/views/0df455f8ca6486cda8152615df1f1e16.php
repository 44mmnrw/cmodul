

<?php $__env->startSection('title', 'Приходы компонентов'); ?>

<?php $__env->startSection('content'); ?>
<div class="receipts-wrapper">
    <!-- Заголовок -->
    <div class="receipts-header">
        <a href="/" class="back-btn" title="На главную">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12l4.58-4.59z" fill="currentColor"/>
            </svg>
        </a>
        
        <div class="header-content">
            <div class="header-icon-box green">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.16-2.66c-.3-.37-.77-.56-1.24-.56-.99 0-1.57 1.14-.82 1.89l2.98 3.67c.35.41.87.67 1.41.67.54 0 1.06-.26 1.41-.67l4.15-5.23c.75-.75.17-1.89-.82-1.89-.48 0-.95.19-1.25.56z" fill="#00a63e"/>
                </svg>
            </div>
            
            <div class="header-text">
                <h1>Приход компонентов</h1>
                <p>Регистрация поступления компонентов из производства</p>
            </div>
        </div>
    </div>

    <!-- Ошибки валидации -->
    <?php if($errors->any()): ?>
        <div class="alert alert-error">
            <div class="alert-title">Ошибка при заполнении формы</div>
            <ul class="alert-list">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Успешное сообщение -->
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <div class="alert-title">Успешно</div>
            <p class="alert-message"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <!-- Форма -->
    <form action="<?php echo e(route('receipts.store')); ?>" method="POST" class="receipt-form">
        <?php echo csrf_field(); ?>

        <div class="form-section">
            <div class="form-header">
                <h2 class="form-title">Детали прихода</h2>
                <p class="form-subtitle">Укажите компоненты и их количество</p>
            </div>

            <!-- Таблица товаров -->
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Компонент</th>
                                <th style="width: 15%;">Дата</th>
                                <th style="width: 15%;">Количество</th>
                                <th style="width: 25%;">Примечания</th>
                                <th style="width: 5%; text-align: center;">Действие</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            <tr class="table-row" data-row-index="0">
                                <td>
                                    <select name="items[0][product_id]" class="form-input" required>
                                        <option value="">-- Выберите компонент --</option>
                                        <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($component->id); ?>">
                                                <?php echo e($component->name); ?>

                                                <?php if($component->stock): ?>
                                                    (Осталось: <?php echo e($component->stock->available ?? 0); ?> шт)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="items[0][date]" class="form-input" value="<?php echo e(date('Y-m-d')); ?>" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-input" placeholder="0" min="1" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][reason]" class="form-input" placeholder="Примечания...">
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn-icon delete-row" onclick="deleteRow(this)" style="display: none;">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Счетчик строк -->
            <div class="row-counter">
                Заполнено строк: <strong><span id="filledCount">0</span>/<span id="totalCount">1</span></strong>
            </div>

            <!-- Кнопка добавления строки -->
            <button type="button" class="btn btn-secondary" onclick="addRow()">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill="currentColor"/>
                </svg>
                Добавить еще один компонент
            </button>
        </div>

        <!-- Кнопки действия -->
        <div class="form-actions">
            <a href="/" class="btn btn-tertiary">Отмена</a>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="currentColor"/>
                </svg>
                Сохранить приход
            </button>
        </div>
    </form>
</div>

<style>
    /* Форма приходов */
    .receipt-form {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .form-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .form-header {
        margin-bottom: 8px;
    }

    .form-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin: 0 0 4px 0;
    }

    .form-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }

    .receipt-table {
        width: 100%;
        border-collapse: collapse;
    }

    .receipt-table thead {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .receipt-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .receipt-table tbody tr:last-child td {
        border-bottom: none;
    }

    .receipt-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid #d1d5dc;
        border-radius: 8px;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-input:focus {
        border-color: #00a63e;
        box-shadow: 0 0 0 3px rgba(0, 166, 62, 0.1);
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: none;
        background-color: transparent;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s ease;
        padding: 0;
    }

    .btn-icon:hover {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .btn-icon svg {
        width: 18px;
        height: 18px;
    }

    .row-counter {
        font-size: 13px;
        color: #6b7280;
        padding: 8px 0;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 500;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .btn svg {
        width: 18px;
        height: 18px;
    }

    .btn-primary {
        background-color: #00a63e;
        color: white;
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background-color: #008a32;
        box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary {
        background-color: white;
        color: #374151;
        border: 1px solid #d1d5dc;
        box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-secondary:hover {
        background-color: #f9fafb;
        border-color: #b4b9c3;
    }

    .btn-tertiary {
        background-color: transparent;
        color: #4b5563;
        border: 1px solid #d1d5dc;
    }

    .btn-tertiary:hover {
        background-color: #f9fafb;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 24px;
    }

    .alert-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .alert-message {
        font-size: 14px;
        margin: 0;
    }

    .alert-list {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }

    .alert-list li {
        font-size: 14px;
        margin: 4px 0;
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #fee2e2;
        color: #991b1b;
    }

    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #dcfce7;
        color: #166534;
    }
</style>

<script>
    let rowIndex = 1;

    function switchTab(tab, button) {
        // Обновить кнопки
        document.querySelectorAll('.receipts-tabs .tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('inactive');
        });
        if (button) {
            button.classList.add('active');
            button.classList.remove('inactive');
        }
    }

    function addRow() {
        const table = document.getElementById('itemsTable');
        const newRow = document.createElement('tr');
        newRow.className = 'table-row';
        newRow.setAttribute('data-row-index', rowIndex);

        newRow.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-input" required>
                    <option value="">-- Выберите компонент --</option>
                    <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($component->id); ?>">
                            <?php echo e($component->name); ?>

                            <?php if($component->stock): ?>
                                (Осталось: <?php echo e($component->stock->available ?? 0); ?> шт)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </td>
            <td>
                <input type="date" name="items[${rowIndex}][date]" class="form-input" value="${new Date().toISOString().split('T')[0]}" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-input" placeholder="0" min="1" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][reason]" class="form-input" placeholder="Примечания...">
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-icon delete-row" onclick="deleteRow(this)">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" fill="currentColor"/>
                    </svg>
                </button>
            </td>
        `;

        table.appendChild(newRow);
        rowIndex++;
        updateRowCounter();
    }

    function deleteRow(button) {
        const row = button.closest('tr');
        const table = document.getElementById('itemsTable');
        
        if (table.querySelectorAll('tr').length > 1) {
            row.remove();
            updateRowCounter();
        } else {
            alert('Должна быть хотя бы одна строка');
        }
    }

    function updateRowCounter() {
        const table = document.getElementById('itemsTable');
        const rows = table.querySelectorAll('tr');
        const totalCount = rows.length;
        
        let filledCount = 0;
        rows.forEach(row => {
            const productSelect = row.querySelector('select[name*="product_id"]');
            const quantityInput = row.querySelector('input[name*="quantity"]');
            if (productSelect && productSelect.value && quantityInput && quantityInput.value) {
                filledCount++;
            }
        });

        document.getElementById('totalCount').textContent = totalCount;
        document.getElementById('filledCount').textContent = filledCount;

        // Показать/скрыть кнопки удаления
        rows.forEach((row, index) => {
            const deleteBtn = row.querySelector('.delete-row');
            if (deleteBtn) {
                deleteBtn.style.display = totalCount > 1 ? 'inline-flex' : 'none';
            }
        });
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        updateRowCounter();

        // Отслеживать изменения для обновления счетчика
        document.getElementById('itemsTable').addEventListener('change', updateRowCounter);
        document.getElementById('itemsTable').addEventListener('input', updateRowCounter);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/receipts/index.blade.php ENDPATH**/ ?>