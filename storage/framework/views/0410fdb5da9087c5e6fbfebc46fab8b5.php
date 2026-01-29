

<?php $__env->startSection('title', 'Добавить деталь'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('details._form', [
    'detail' => null,
    'action' => route('details.store'),
    'title' => 'Добавить деталь',
    'backRoute' => route('details.index'),
    'categories' => $categories,
    'sources' => $sources
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/details/create.blade.php ENDPATH**/ ?>