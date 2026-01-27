


<?php $__env->startSection('content'); ?>
<?php echo $__env->make('configurations._form', [
    'cabinet' => null,
    'action' => route('cabinet.store'),
    'backRoute' => route('configurations.index', ['type' => $productType]),
    'currentComponents' => [],
    'availableComponents' => $availableComponents,
    'productType' => $productType,
    'productTypes' => $productTypes
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Cmodul\resources\views/configurations/create.blade.php ENDPATH**/ ?>