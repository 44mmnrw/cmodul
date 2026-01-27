<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfigController;

Route::get('/', function () {
    return view('index');
});

Route::get('/places', function () {
    $items = \App\Models\Detail::where('product_type_id', 2)
        ->with('productType', 'category')
        ->paginate(10);
    return view('details.list', [
        'items' => $items,
        'pageTitle' => 'Компоненты',
        'pageSubtitle' => 'Управление компонентами и местами размещения',
        'addButtonText' => 'Добавить компонент',
        'addButtonUrl' => route('details.create'),
        'emptyMessage' => 'Компоненты не найдены.'
    ]);
});

Route::get('/virtual-stock', function () {
    $configurations = \App\Models\Detail::where('product_type_id', 1)
        ->with('productType')
        ->get();
    return view('virtual-stock', ['configurations' => $configurations]);
});

Route::get('/configurations', function () {
    $type = request('type', 1); // Тип по умолчанию 1 (конфигурации)
    $cabinets = \App\Models\Detail::where('product_type_id', $type)
        ->with('productType')
        ->paginate(10);
    return view('configurations.list', [
        'cabinets' => $cabinets,
        'currentType' => $type
    ]);
})->name('configurations.index');

Route::get('/configurations/create', [ConfigController::class, 'create'])->name('configurations.create');

Route::get('/configurations/{id}', function ($id) {
    $cabinet = \App\Models\Detail::where('product_type_id', 1)
        ->with('componentsInConfiguration', 'productType')
        ->findOrFail($id);
    return view('configurations.show', ['cabinet' => $cabinet]);
})->name('configuration-detail');

Route::post('/configurations', [ConfigController::class, 'store'])->name('cabinet.store');
Route::get('/configurations/{id}/edit', [ConfigController::class, 'edit'])->name('configurations.edit');
Route::put('/configurations/{id}', [ConfigController::class, 'update'])->name('configuration-update');
Route::delete('/configurations/{id}', [ConfigController::class, 'destroy'])->name('configurations.destroy');

// Маршруты для кабинетов
Route::resource('cabinets', CabinetController::class);

// Маршруты для пользователей
Route::resource('users', UserController::class);

// Маршруты для деталей
Route::resource('details', \App\Http\Controllers\DetailController::class);

