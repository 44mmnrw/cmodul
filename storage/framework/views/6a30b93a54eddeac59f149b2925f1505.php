

<?php $__env->startSection('title', $title ?? 'Список'); ?>

<?php $__env->startSection('content'); ?>
<div class="details-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title"><?php echo e($pageTitle ?? 'Список'); ?></h1>
            <p class="page-subtitle"><?php echo e($pageSubtitle ?? 'Управление элементами'); ?></p>
        </div>
        <?php if($addButtonUrl ?? false): ?>
            <a href="<?php echo e($addButtonUrl); ?>" class="btn-primary">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                </svg>
                <?php echo e($addButtonText ?? 'Добавить'); ?>

            </a>
        <?php endif; ?>
    </div>

    <!-- Details Table -->
    <div class="card details-card">
        <div class="table-wrapper">
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Код</th>
                        <th>Название</th>
                        <th class="text-right">Остаток</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <?php if($item->category): ?>
                                    <span class="category-badge"><?php echo e($item->category->name); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code class="detail-code"><?php echo e($item->scu); ?></code>
                            </td>
                            <td>
                                <a href="<?php echo e(route('details.show', $item)); ?>" class="detail-link">
                                    <?php echo e($item->name); ?>

                                </a>
                            </td>
                            <td class="text-right">
                                <span class="text-muted">—</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('details.show', $item)); ?>" class="btn-icon btn-view" title="Просмотр">
                                        <svg class="icon">
                                            <use xlink:href="#icon-eye"></use>
                                        </svg>
                                    </a>
                                    <a href="<?php echo e(route('details.edit', $item)); ?>" class="btn-icon btn-edit" title="Редактировать">
                                        <svg class="icon">
                                            <use xlink:href="#icon-pencil"></use>
                                        </svg>
                                    </a>
                                    <form action="<?php echo e(route('details.destroy', $item)); ?>" method="POST" class="delete-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-icon btn-delete" title="Удалить" onclick="return confirm('Вы уверены?')">
                                            <svg class="icon">
                                                <use xlink:href="#icon-delete"></use>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <?php echo e($emptyMessage ?? 'Элементы не найдены.'); ?> <a href="<?php echo e($addButtonUrl ?? '#'); ?>">Добавить новый</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if(isset($items) && method_exists($items, 'hasPages') && $items->hasPages()): ?>
        <div class="pagination-wrapper">
            <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
                <!-- Previous Page Link -->
                <?php if($items->onFirstPage()): ?>
                    <span class="pagination-item disabled">← Назад</span>
                <?php else: ?>
                    <a href="<?php echo e($items->previousPageUrl()); ?>" class="pagination-item">← Назад</a>
                <?php endif; ?>

                <!-- Pagination Elements -->
                <div class="pagination-numbers">
                    <?php $__currentLoopData = $items->getUrlRange(1, $items->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $items->currentPage()): ?>
                            <span class="pagination-item active"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>" class="pagination-item"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Next Page Link -->
                <?php if($items->hasMorePages()): ?>
                    <a href="<?php echo e($items->nextPageUrl()); ?>" class="pagination-item">Далее →</a>
                <?php else: ?>
                    <span class="pagination-item disabled">Далее →</span>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/details/list.blade.php ENDPATH**/ ?>