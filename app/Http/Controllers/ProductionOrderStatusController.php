<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrderStatus;
use Illuminate\Http\Request;

class ProductionOrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = ProductionOrderStatus::orderBy('sort_order')->get();
        return view('production-order-statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $status = null;
        return view('production-order-statuses.form', compact('status'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'required|integer|min:0',
        ]);

        ProductionOrderStatus::create($validated);

        return redirect()->route('production-order-statuses.index')
            ->with('success', 'Статус успешно добавлен');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductionOrderStatus $productionOrderStatus)
    {
        $status = $productionOrderStatus;
        return view('production-order-statuses.form', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductionOrderStatus $productionOrderStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'required|integer|min:0',
        ]);

        $productionOrderStatus->update($validated);

        return redirect()->route('production-order-statuses.index')
            ->with('success', 'Статус успешно обновлён');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductionOrderStatus $productionOrderStatus)
    {
        // Проверяем, использует ли статус какой-то заказ
        $ordersCount = \App\Models\ProductionOrder::where('status_id', $productionOrderStatus->id)->count();
        
        if ($ordersCount > 0) {
            return redirect()->route('production-order-statuses.index')
                ->with('error', "Статус используется в $ordersCount заказе(ах) и не может быть удалён");
        }

        $productionOrderStatus->delete();

        return redirect()->route('production-order-statuses.index')
            ->with('success', 'Статус успешно удалён');
    }
}
