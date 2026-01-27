<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('index');
});

Route::get('/places', function () {
    $places = \App\Models\Place::all();
    return view('places', ['places' => $places]);
});

Route::get('/virtual-stock', function () {
    $configurations = \App\Models\Detail::where('product_type_id', 1)
        ->with('productType')
        ->get();
    return view('virtual-stock', ['configurations' => $configurations]);
});

Route::get('/configurations', function () {
    $cabinets = \App\Models\Detail::where('product_type_id', 1)
        ->with('productType')
        ->get();
    return view('configurations', ['cabinets' => $cabinets]);
});

Route::get('/configurations/{id}', function ($id) {
    $cabinet = \App\Models\Detail::where('product_type_id', 1)
        ->with('componentsInConfiguration', 'productType')
        ->findOrFail($id);
    return view('configuration-detail', ['cabinet' => $cabinet]);
});

Route::get('/places/{place_id}', function ($place_id) {
    $place = \App\Models\Place::where('place_id', $place_id)
        ->with('cabinets', 'stock', 'details')
        ->firstOrFail();
    
    // Загрузим деталь по place_id в details таблице
    $placeDetail = \App\Models\Detail::where('scu', $place_id)
        ->with('componentsInConfiguration', 'usedInCabinets', 'productType')
        ->first();
    
    return view('place-detail', ['place' => $place, 'placeDetail' => $placeDetail]);
});

Route::get('/test-detail/{id}', function($id) {
    $detail = \App\Models\Detail::with('places')->findOrFail($id);
    return view('test-detail', compact('detail'));
});

// Маршруты для мест
Route::resource('places', PlaceController::class)->parameters([
    'place' => 'place:place_id'
]);

// Маршруты для кабинетов
Route::resource('cabinets', CabinetController::class);

// Маршруты для пользователей
Route::resource('users', UserController::class);

// Маршруты для деталей
Route::resource('details', \App\Http\Controllers\DetailController::class);
