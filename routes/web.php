<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StockBalanceController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ProductionOrderStatusController;
use App\Http\Controllers\ProductionPlanningController;

Route::get('/', function () {
    return view('index');
});

Route::get('/items', function (Request $request) {
    $type = $request->get('type', 2); // Тип по умолчанию 2 (компоненты)
    $sort = $request->get('sort', 'name');
    $direction = $request->get('direction', 'asc');
    
    // Проверяем тип
    if (!in_array($type, [1, 2, 3])) {
        $type = 2;
    }
    
    $allowedSorts = ['name', 'scu', 'category_id', 'created_at'];
    if (!in_array($sort, $allowedSorts)) {
        $sort = 'name';
    }
    if (!in_array($direction, ['asc', 'desc'])) {
        $direction = 'asc';
    }
    
    $items = \App\Models\Detail::where('product_type_id', $type)
        ->with('productType', 'category', 'stock')
        ->orderBy($sort, $direction)
        ->paginate(10)
        ->appends($request->query());
    
    $typeNames = [1 => 'Шкафы', 2 => 'Компоненты', 3 => 'Детали'];
    
    return view('details.list', [
        'items' => $items,
        'pageTitle' => 'Изделия',
        'pageSubtitle' => 'Управление ' . strtolower($typeNames[$type]),
        'addButtonText' => $type == 2 ? 'Добавить компонент' : ($type == 3 ? 'Добавить деталь' : 'Добавить шкаф'),
        'addButtonUrl' => $type == 1 ? route('configurations.create') : route('details.create'),
        'emptyMessage' => $typeNames[$type] . ' не найдены.',
        'currentSort' => $sort,
        'currentDirection' => $direction,
        'sortBaseUrl' => '/items',
        'currentType' => $type,
        'typeNames' => $typeNames,
    ]);
});

// Оставляем /places как редирект для совместимости
Route::get('/places', function (Request $request) {
    return redirect('/items?type=2' . ($request->getQueryString() ? '&' . $request->getQueryString() : ''));
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
    $cabinet = \App\Models\Detail::whereIn('product_type_id', [1, 2])
        ->with('componentsInConfiguration', 'productType', 'stock')
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

// Маршруты для статусов производственных заказов
Route::resource('production-order-statuses', ProductionOrderStatusController::class);

// Маршруты для планирования производства
Route::get('/production-planning', [ProductionPlanningController::class, 'index'])->name('production-planning.index');
Route::post('/production-planning/analyze', [ProductionPlanningController::class, 'analyzeRequirements'])->name('production-planning.analyze');
Route::post('/production-planning/approve', [ProductionPlanningController::class, 'approvePlan'])->name('production-planning.approve');

