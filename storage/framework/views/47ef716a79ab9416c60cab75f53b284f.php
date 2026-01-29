

<?php $__env->startSection('title', 'Производственный заказ ' . $orders->first()?->order->order_num); ?>

<?php $__env->startSection('content'); ?>
<div class="production-order-detail-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">Производственный заказ</h1>
            <p class="page-subtitle"><?php echo e($orders->first()?->order->order_num); ?> · <?php echo e($orders->first()?->created_at->format('d.m.Y')); ?></p>
        </div>
        <div class="header-actions">
            <a href="<?php echo e(route('production-orders.index')); ?>" class="btn-secondary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-arrow-left"></use>
                </svg>
                <span>Назад</span>
            </a>
            <a href="<?php echo e(route('production-orders.create')); ?>" class="btn-secondary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-plus"></use>
                </svg>
                <span>Новый заказ</span>
            </a>
            <a href="<?php echo e(route('production-orders.edit', $orders->first())); ?>" class="btn-primary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-pencil"></use>
                </svg>
                <span>Редактировать</span>
            </a>
        </div>
    </div>

    <!-- Order Info Table -->
    <div class="order-info-table-wrapper">
        <table class="order-info-table">
            <tbody>
                <tr>
                    <td class="info-label">Дата заказа</td>
                    <td class="info-value"><?php echo e($orders->first()?->created_at->format('d.m.Y H:i')); ?></td>
                </tr>
                <tr>
                    <td class="info-label">Примечания</td>
                    <td class="info-value"><?php echo e($orders->first()?->notes ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="info-label">Статус</td>
                    <td class="info-value">
                        <?php if($orders->first()?->orderStatus): ?>
                            <span class="status-badge" style="background-color: <?php echo e($orders->first()?->orderStatus->color); ?>;">
                                <?php echo e($orders->first()?->orderStatus->name); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-muted">Не указан</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Configurations Table -->
    <div class="configs-list-wrapper">
        <h3 class="configs-list-title">Конфигурации к производству</h3>
        <div class="table-wrapper">
            <table class="configs-list-table">
                <thead>
                    <tr>
                        <th class="col-number">№</th>
                        <th class="col-config">Конфигурация</th>
                        <th class="col-quantity">Заказано</th>
                        <th class="col-received">Получено</th>
                        <th class="col-date">Плановая дата</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="col-number"><?php echo e($index + 1); ?></td>
                            <td class="col-config"><?php echo e($order->product->name); ?></td>
                            <td class="col-quantity"><?php echo e($order->quantity_ordered); ?> шт</td>
                            <td class="col-received"><?php echo e($order->quantity_received); ?> шт</td>
                            <td class="col-date">
                                <?php if($order->planned_date): ?>
                                    <?php echo e($order->planned_date->format('d.m.Y')); ?>

                                <?php else: ?>
                                    <span class="text-muted">не установлена</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Нет конфигураций</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Button -->
    <div class="mt-6">
        <form action="<?php echo e(route('production-orders.destroy', $orders->first())); ?>" method="POST" class="delete-form" onsubmit="return confirm('Вы уверены? Это действие необратимо.')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-danger">
                <svg class="btn-icon">
                    <use xlink:href="#icon-delete"></use>
                </svg>
                <span>Удалить заказ</span>
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/production-orders/show.blade.php ENDPATH**/ ?>