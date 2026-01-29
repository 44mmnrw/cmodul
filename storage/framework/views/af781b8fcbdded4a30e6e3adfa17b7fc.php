

<?php $__env->startSection('title', 'Производственные заказы'); ?>

<?php $__env->startSection('content'); ?>
<div class="production-orders-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Производственные заказы</h1>
            <p class="page-subtitle">Размещение и управление заказами на производство шкафов</p>
        </div>
        <a href="<?php echo e(route('production-orders.create')); ?>" class="btn-primary btn-create-config">
            <svg class="btn-icon">
                <use xlink:href="#icon-plus"></use>
            </svg>
            <span>Новый заказ</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Всего</p>
                <p class="stat-value"><?php echo e($stats['total']); ?></p>
            </div>
            <svg class="stat-icon stat-icon-primary">
                <use xlink:href="#icon-lightning"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Ожидает</p>
                <p class="stat-value"><?php echo e($stats['ordering']); ?></p>
            </div>
            <svg class="stat-icon stat-icon-warning">
                <use xlink:href="#icon-clock"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">В работе</p>
                <p class="stat-value"><?php echo e($stats['in_production']); ?></p>
            </div>
            <svg class="stat-icon stat-icon-info">
                <use xlink:href="#icon-settings"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Готов</p>
                <p class="stat-value"><?php echo e($stats['ready']); ?></p>
            </div>
            <svg class="stat-icon stat-icon-success">
                <use xlink:href="#icon-check"></use>
            </svg>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <p class="stat-label">Завершен</p>
                <p class="stat-value"><?php echo e($stats['completed']); ?></p>
            </div>
            <svg class="stat-icon stat-icon-success">
                <use xlink:href="#icon-verified"></use>
            </svg>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card filters-card">
        <div class="filters-header">
            <svg class="filter-icon">
                <use xlink:href="#icon-filter"></use>
            </svg>
            <span class="filter-label">Статус:</span>
            <form method="GET" class="filter-buttons-group">
                <button type="submit" name="status" value="all" class="filter-button <?php echo e($currentStatus === 'all' ? 'active' : ''); ?>">
                    Все
                </button>
                <button type="submit" name="status" value="ordering" class="filter-button <?php echo e($currentStatus === 'ordering' ? 'active' : ''); ?>">
                    Ожидает
                </button>
                <button type="submit" name="status" value="in_production" class="filter-button <?php echo e($currentStatus === 'in_production' ? 'active' : ''); ?>">
                    В работе
                </button>
                <button type="submit" name="status" value="ready" class="filter-button <?php echo e($currentStatus === 'ready' ? 'active' : ''); ?>">
                    Готов
                </button>
                <button type="submit" name="status" value="completed" class="filter-button <?php echo e($currentStatus === 'completed' ? 'active' : ''); ?>">
                    Завершен
                </button>
            </form>
        </div>

        <div class="search-wrapper">
            <svg class="search-icon">
                <use xlink:href="#icon-search"></use>
            </svg>
            <form method="GET" class="search-form">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Поиск по номеру заказа, конфигурации или примечаниям..." class="search-input">
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card production-orders-card">
        <div class="card-header">
            <h3 class="card-title">Список заказов</h3>
        </div>

        <?php if($orders->count()): ?>
            <table class="production-table">
                <thead>
                    <tr>
                        <th>Номер заказа</th>
                        <th>Дата / Срок</th>
                        <th>План. дата</th>
                        <th class="text-center">Конфигов</th>
                        <th class="text-center">Кол-во</th>
                        <th class="text-right">Сумма</th>
                        <th class="text-center">Статус</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong class="order-number"><?php echo e($order->order->order_num); ?></strong>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <span class="date-value"><?php echo e($order->created_at->format('d.m.Y')); ?></span>
                                    <span class="date-deadline">до <?php echo e($order->created_at->addMonths(2)->format('d.m.Y')); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if($order->planned_date): ?>
                                    <span class="planned-date"><?php echo e(\Carbon\Carbon::parse($order->planned_date)->format('d.m.Y')); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="qty-value"><?php echo e($order->config_count); ?> шт</span>
                            </td>
                            <td class="text-center">
                                <span class="qty-value"><?php echo e($order->total_quantity); ?> шт</span>
                            </td>
                            <td class="text-right">
                                <strong class="price-value"><?php echo e(number_format($order->total_quantity * 13550, 0, ',', ' ')); ?> ₽</strong>
                            </td>
                            <td class="text-center">
                                <?php if($order->orderStatus): ?>
                                    <span class="status-badge" style="background-color: <?php echo e($order->orderStatus->color); ?>;">
                                        <?php echo e($order->orderStatus->name); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="status-badge">Не указан</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('production-orders.show', $order->id)); ?>" class="btn-icon btn-view" title="Просмотр">
                                        <svg class="icon">
                                            <use xlink:href="#icon-arrow-right"></use>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <?php if($orders->hasPages()): ?>
                <div class="pagination-wrapper">
                    <?php echo e($orders->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <svg class="empty-icon">
                    <use xlink:href="#icon-inbox"></use>
                </svg>
                <p class="empty-message">Заказы не найдены</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/production-orders/index.blade.php ENDPATH**/ ?>