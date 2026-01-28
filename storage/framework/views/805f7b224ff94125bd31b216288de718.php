

<?php $__env->startSection('title', 'История приходов'); ?>

<?php $__env->startSection('content'); ?>
<div class="receipts-wrapper">
    <!-- Заголовок страницы -->
    <div class="receipts-header">
        <a href="<?php echo e($backUrl ?? '/receipts'); ?>" class="back-btn" title="Назад">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12l4.58-4.59z" fill="currentColor"/>
            </svg>
        </a>
        
        <div class="header-content">
            <div class="header-icon-box green">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-9h-1V2h-2v1H9V2H7v1H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H6V7h12v12z" fill="#00a63e"/>
                </svg>
            </div>
            
            <div class="header-text">
                <h1>Приход компонентов</h1>
                <p>Регистрация поступления компонентов из производства</p>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="stats-grid">
        <!-- Всего операций -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Всего операций</div>
                <div class="stat-value"><?php echo e($totalOperations ?? 0); ?></div>
            </div>
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 9h4v2h-4v4h-2v-4H8v-2h4v-4h2v4z" fill="#1447e6"/>
                </svg>
            </div>
        </div>

        <!-- Принято компонентов -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Принято компонентов</div>
                <div class="stat-value"><?php echo e($totalQuantity ?? 0); ?><small>шт</small></div>
            </div>
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16v-2h-3V9h2V7h-2V4h-2v3h-4V4h-2v3H9.5C8.1 4 7 5.1 7 6.5v5C7 13 6 14 4.5 14S2 13 2 11.5h-2C0 14.3 1.7 16.7 4.5 16.7c.9 0 1.7-.2 2.5-.6.6 1.5 2 2.6 3.7 2.6h7.5l2 2h3V16h-3z" fill="#00a63e"/>
                </svg>
            </div>
        </div>

        <!-- Типов компонентов -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Типов компонентов</div>
                <div class="stat-value"><?php echo e($uniqueComponentsCount ?? 0); ?></div>
            </div>
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#7c3aed"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="filters-section">
        <div class="period-filter">
            <div class="period-label">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
                </svg>
                Период:
            </div>
            
            <div class="period-buttons">
                <button class="period-btn active" onclick="filterByPeriod('all', this)">
                    Все время
                </button>
                <button class="period-btn inactive" onclick="filterByPeriod('today', this)">
                    Сегодня
                </button>
                <button class="period-btn inactive" onclick="filterByPeriod('week', this)">
                    Неделя
                </button>
                <button class="period-btn inactive" onclick="filterByPeriod('month', this)">
                    Месяц
                </button>
            </div>
        </div>

        <!-- Поиск -->
        <div class="search-wrapper">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
            </svg>
            <input type="text" class="search-input" placeholder="Поиск по компоненту или примечаниям...">
        </div>
    </div>

    <!-- Таблица сводки по компонентам -->
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">Сводка по компонентам</h2>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Компонент</th>
                        <th>Операций</th>
                        <th>Принято всего</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $componentSummary ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="cell-primary"><?php echo e($summary['name'] ?? 'N/A'); ?></div>
                                <div class="cell-secondary"><?php echo e($summary['id'] ?? ''); ?></div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge blue"><?php echo e($summary['count'] ?? 0); ?></span>
                            </td>
                            <td style="text-align: right;">
                                <span class="cell-primary"><?php echo e($summary['quantity'] ?? 0); ?> шт</span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 48px 24px; color: #9ca3af;">
                                Нет данных для отображения
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Журнал операций -->
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">Журнал операций</h2>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Компонент</th>
                        <th style="text-align: center;">Количество</th>
                        <th>Примечания</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $movements ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="date-cell">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-9h-1V2h-2v1H9V2H7v1H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H6V7h12v12z" fill="currentColor"/>
                                    </svg>
                                    <span><?php echo e($movement->created_at->format('d.m.Y')); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="cell-primary"><?php echo e($movement->product->name ?? 'N/A'); ?></div>
                                <div class="cell-secondary"><?php echo e($movement->product->id ?? ''); ?></div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge green">+<?php echo e($movement->quantity); ?> шт</span>
                            </td>
                            <td>
                                <span style="color: #6b7280;"><?php echo e($movement->reason ?? '—'); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 48px 24px; color: #9ca3af;">
                                Нет операций
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Пагинация -->
    <?php if($movements && method_exists($movements, 'hasPages') && $movements->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($movements->links()); ?>

        </div>
    <?php endif; ?>
</div>

<script>
    function filterByPeriod(period, button) {
        // Обновить активную кнопку
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('inactive');
        });
        button.classList.add('active');
        button.classList.remove('inactive');
        
        console.log('Filter by period:', period);
        // TODO: Реализация фильтрации по периодам
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/receipts/history.blade.php ENDPATH**/ ?>