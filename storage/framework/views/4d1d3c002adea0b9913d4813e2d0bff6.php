

<?php $__env->startSection('title', 'Редактировать ' . $detail->name); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('details._form', [
    'detail' => $detail,
    'action' => route('details.update', $detail),
    'title' => 'Редактировать',
    'backRoute' => route('details.show', $detail),
    'categories' => $categories,
    'sources' => $sources
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/details/edit.blade.php ENDPATH**/ ?>