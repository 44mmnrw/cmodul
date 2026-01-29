<!-- Navigation -->
<nav class="nav-container">
    <div class="nav-wrapper">
        <a href="/" class="nav-button {{ request()->is('/') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 13h8v8H3zm10 0h8v8h-8zm-10-10h8v8H3z" fill="currentColor"/>
            </svg>
            <span>Дашборд</span>
        </a>

        <a href="/items" class="nav-button {{ request()->is('items*', 'places*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 14a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" fill="currentColor"/>
            </svg>
            <span>Изделия</span>
        </a>

        <a href="/configurations" class="nav-button {{ request()->is('configurations*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.5 12c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5zm-9 0c0-.83-.67-1.5-1.5-1.5S7 11.17 7 12s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5zm9-7H3.5C2.12 5 1 6.12 1 7.5v9C1 17.88 2.12 19 3.5 19h13C17.88 19 19 17.88 19 16.5v-9C19 6.12 17.88 5 16.5 5zm0 10.5h-13v-9h13v9zm-6-5.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5z" fill="currentColor"/>
            </svg>
            <span>Конфигурации</span>
        </a>

        <a href="/virtual-stock" class="nav-button {{ request()->is('virtual-stock*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 13h2v8H3zm4-8h2v16H7zm4-2h2v18h-2zm4 4h2v14h-2zm4-4h2v18h-2z" fill="currentColor"/>
            </svg>
            <span>Виртуальные остатки</span>
        </a>

        <a href="/receipts/journal" class="nav-button {{ request()->is('receipts*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-2.16-2.66c-.3-.37-.77-.56-1.24-.56-.99 0-1.57 1.14-.82 1.89l2.98 3.67c.35.41.87.67 1.41.67.54 0 1.06-.26 1.41-.67l4.15-5.23c.75-.75.17-1.89-.82-1.89-.48 0-.95.19-1.25.56z" fill="currentColor"/>
            </svg>
            <span>Приходы</span>
        </a>

        <a href="/shipments" class="nav-button {{ request()->is('shipments*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 18.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM9 18.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" fill="currentColor"/>
                <path d="M20 8H4V4h16m0 7H1v9h22v-9z" fill="currentColor"/>
            </svg>
            <span>Отгрузки</span>
        </a>

        <a href="{{ route('production-orders.index') }}" class="nav-button {{ request()->is('production-orders*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 9.5c0 .83-.67 1.5-1.5 1.5S11 13.33 11 12.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5zM5 5h14v4H5V5zm0 14v-4h14v4H5z" fill="currentColor"/>
            </svg>
            <span>Производство</span>
        </a>

        <a href="{{ route('production-planning.index') }}" class="nav-button {{ request()->is('production-planning*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 13h2v8H3zm4-8h2v16H7zm4-2h2v18h-2zm4 4h2v14h-2zm4-4h2v18h-2z" fill="currentColor"/>
            </svg>
            <span>Планирование</span>
        </a>

        <a href="{{ route('production-order-statuses.index') }}" class="nav-button {{ request()->is('production-order-statuses*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
            </svg>
            <span>Статусы</span>
        </a>
    </div>
</nav>
