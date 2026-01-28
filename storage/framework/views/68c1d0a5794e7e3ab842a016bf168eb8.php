

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="title-section">
                <h1 class="page-title">Виртуальные остатки шкафов</h1>
                <p class="page-subtitle">Рассчитанные остатки готовых конфигураций на основе доступных компонентов</p>
            </div>
            <button class="btn-primary btn-lg">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                Автоматический расчет
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Configs -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего конфигураций</p>
                <p class="stat-value">5</p>
            </div>
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Общая стоимость</p>
                <p class="stat-value">3095к ₽</p>
            </div>
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4-2.67z"/>
                </svg>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Низкий запас</p>
                <p class="stat-value stat-value-warning">0</p>
            </div>
            <div class="stat-icon stat-icon-warning">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 4c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2s2-.9 2-2v-4c0-1.1-.9-2-2-2z"/>
                </svg>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Нет в наличии</p>
                <p class="stat-value stat-value-danger">0</p>
            </div>
            <div class="stat-icon stat-icon-danger">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card search-filter-card">
        <div class="search-input-wrapper">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input 
                type="text" 
                class="search-input" 
                placeholder="Поиск по названию, коду или описанию..."
            >
        </div>
    </div>

    <!-- Table Section -->
    <div class="card table-card">
        <div class="table-wrapper">
            <table class="configurations-table">
                <thead>
                    <tr>
                        <th>Конфигурация</th>
                        <th>Виртуальный остаток</th>
                        <th>Статус</th>
                        <th>Лимитирующий компонент</th>
                        <th class="text-right">Цена за шт.</th>
                        <th class="text-right">Общая стоимость</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $configurations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="config-cell">
                                <div class="config-name"><?php echo e($config->name); ?></div>
                                <div class="config-code"><?php echo e($config->cabinet_id); ?></div>
                                <div class="config-description"><?php echo e($config->description ?? 'Нет описания'); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="stock-display">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="stock-icon">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6l5.25 3.15-.75 1.23-4.5-2.67z"/>
                                </svg>
                                <span class="stock-number"><?php echo e($config->virtual_stock ?? '-'); ?></span>
                                <span class="stock-unit">шт</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo e($config->status_class ?? 'high'); ?>"><?php echo e($config->status ?? 'Неизвестно'); ?></span>
                        </td>
                        <td>
                            <div class="limiting-component">
                                <div class="component-name"><?php echo e($config->limiting_component ?? '-'); ?></div>
                                <div class="component-info">Доступно: <?php echo e($config->available ?? '-'); ?> шт</div>
                                <div class="component-info">Требуется на 1 шт: <?php echo e($config->required ?? '-'); ?> шт</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <span class="price"><?php echo e($config->unit_price ?? '-'); ?></span>
                        </td>
                        <td class="text-right">
                            <span class="price"><?php echo e($config->total_price ?? '-'); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            Нет данных
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Block -->
    <div class="info-block">
        <svg class="info-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 4c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2s2-.9 2-2v-4c0-1.1-.9-2-2-2z"/>
        </svg>
        <div class="info-content">
            <h4 class="info-title">Как рассчитываются виртуальные остатки?</h4>
            <p class="info-description">
                Виртуальный остаток показывает, сколько готовых шкафов можно собрать из имеющихся на складе компонентов. 
                Система автоматически определяет лимитирующий компонент — деталь с наименьшим остатком относительно требуемого количества.
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/virtual-stock.blade.php ENDPATH**/ ?>