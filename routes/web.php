<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StockBalanceController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ProductionOrderController;

Route::get('/', function () {
    return view('index');
});

Route::get('/places', function () {
    $items = \App\Models\Detail::where('product_type_id', 2)
        ->with('productType', 'category', 'stock')
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

Route::get('/virtual-stock', [StockBalanceController::class, 'virtualStock'])->name('stock.virtual-stock');
Route::get('/api/virtual-stock', [StockBalanceController::class, 'apiVirtualStock'])->name('api.virtual-stock');

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

// Маршруты для приходов
Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
Route::get('/receipts/history', [ReceiptController::class, 'history'])->name('receipts.history');
Route::get('/receipts/journal', [ReceiptController::class, 'journal'])->name('receipts.journal');
Route::get('/receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');

// Маршруты для отгрузок
Route::get('/shipments', [ShipmentController::class, 'journal'])->name('shipments.journal');
Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
Route::get('/shipments/{id}', [ShipmentController::class, 'show'])->name('shipments.show');

// Маршруты для производственных заказов
Route::resource('production-orders', ProductionOrderController::class);
Route::patch('/production-orders/{productionOrder}/status', [ProductionOrderController::class, 'updateStatus'])->name('production-orders.updateStatus');
Route::post('/production-orders/{productionOrder}/receive', [ProductionOrderController::class, 'receiveQuantity'])->name('production-orders.receiveQuantity');
