<!-- Navigation -->
<nav class="nav-container">
    <div class="nav-wrapper">
        <a href="/" class="nav-button <?php echo e(request()->is('/') ? 'active' : ''); ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 13h8v8H3zm10 0h8v8h-8zm-10-10h8v8H3z" fill="currentColor"/>
            </svg>
            <span>Дашборд</span>
        </a>

        <a href="/places" class="nav-button <?php echo e(request()->is('places*') ? 'active' : ''); ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" fill="currentColor"/>
            </svg>
            <span>Компоненты</span>
        </a>

        <a href="/details" class="nav-button <?php echo e(request()->is('details*') ? 'active' : ''); ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 14a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" fill="currentColor"/>
            </svg>
            <span>Детали</span>
        </a>

        <a href="/configurations" class="nav-button <?php echo e(request()->is('configurations*') ? 'active' : ''); ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.5 12c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5zm-9 0c0-.83-.67-1.5-1.5-1.5S7 11.17 7 12s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5zm9-7H3.5C2.12 5 1 6.12 1 7.5v9C1 17.88 2.12 19 3.5 19h13C17.88 19 19 17.88 19 16.5v-9C19 6.12 17.88 5 16.5 5zm0 10.5h-13v-9h13v9zm-6-5.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5z" fill="currentColor"/>
            </svg>
            <span>Конфигурации</span>
        </a>

        <a href="#" class="nav-button">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z" fill="currentColor"/>
            </svg>
            <span>Остатки</span>
        </a>

        <a href="/virtual-stock" class="nav-button <?php echo e(request()->is('virtual-stock*') ? 'active' : ''); ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 13h2v8H3zm4-8h2v16H7zm4-2h2v18h-2zm4 4h2v14h-2zm4-4h2v18h-2z" fill="currentColor"/>
            </svg>
            <span>Виртуальные остатки</span>
        </a>
    </div>
</nav>
<?php /**PATH C:\laragon\www\Cmodul\resources\views/components/navigation.blade.php ENDPATH**/ ?>