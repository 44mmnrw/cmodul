<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'TelecomCabinet Pro'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/app.css',
        'resources/css/modals.css',
        'resources/js/app.js'
    ]); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- SVG Sprite Icons -->
    <?php echo $__env->make('components.svg-sprite', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="header-logo-section">
                <div class="header-icon">
                    <img src="data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='32' height='32' rx='6' fill='%233366FF'/%3E%3Cpath d='M10 16H22M16 10V22' stroke='white' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E" alt="Logo">
                </div>
                <div class="header-text">
                    <p class="header-title">TelecomCabinet Pro</p>
                    <p class="header-subtitle">Управление виртуальными остатками</p>
                </div>
            </div>

            <div class="header-user-section">
                <p class="header-user-name">Администратор</p>
                <p class="header-user-email">admin@telecom.ru</p>
            </div>
        </div>
    </header>

    <?php echo $__env->make('components.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div class="main-container">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Cmodul\resources\views/layout.blade.php ENDPATH**/ ?>