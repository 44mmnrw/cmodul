<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TelecomCabinet Pro')</title>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
    @stack('styles')
</head>
<body>
    <!-- SVG Sprite Icons -->
    @include('components.svg-sprite')

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

    @include('components.navigation')

    <!-- Main Content -->
    <div class="main-container">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
