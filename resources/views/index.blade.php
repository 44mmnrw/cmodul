@extends('layout')

@section('content')
<div class="content-wrapper">
    <!-- Dashboard Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="title-section">
                <h1 class="page-title">Дашборд</h1>
                <p class="page-subtitle">Добро пожаловать в систему управления</p>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div class="card" style="padding: 20px; text-align: center;">
            <h3 style="color: var(--text-primary); margin: 0 0 10px 0;">Всего мест</h3>
            <p style="font-size: 32px; font-weight: bold; color: var(--primary-color); margin: 0;">39</p>
        </div>
        <div class="card" style="padding: 20px; text-align: center;">
            <h3 style="color: var(--text-primary); margin: 0 0 10px 0;">Всего кабинетов</h3>
            <p style="font-size: 32px; font-weight: bold; color: var(--primary-color); margin: 0;">60</p>
        </div>
        <div class="card" style="padding: 20px; text-align: center;">
            <h3 style="color: var(--text-primary); margin: 0 0 10px 0;">Связи</h3>
            <p style="font-size: 32px; font-weight: bold; color: var(--primary-color); margin: 0;">180</p>
        </div>
    </div>
</div>
@endsection
