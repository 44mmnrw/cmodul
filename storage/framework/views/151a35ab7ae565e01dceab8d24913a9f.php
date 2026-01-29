

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('configurations._form', [
    'cabinet' => $cabinet,
    'action' => route('configuration-update', $cabinet->id),
    'backRoute' => route('configuration-detail', $cabinet->id),
    'currentComponents' => $currentComponents,
    'availableComponents' => $availableComponents,
    'productTypes' => $productTypes
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/configurations/edit.blade.php ENDPATH**/ ?>