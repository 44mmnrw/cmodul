<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Detail;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    /**
     * Показать список всех производственных заказов
     */
    public function index(Request $request)
    {
        $query = ProductionOrder::with('product');
        
        // Фильтр по статусу
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Поиск по компоненту или примечаниям
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })->orWhere('notes', 'like', "%$search%");
        }
        
        $orders = $query->orderByDesc('created_at')->paginate(10);
        
        // Статистика по статусам
        $stats = [
            'total' => ProductionOrder::count(),
            'ordering' => ProductionOrder::where('status', 'ordering')->count(),
            'in_production' => ProductionOrder::where('status', 'in_production')->count(),
            'ready' => ProductionOrder::where('status', 'ready')->count(),
            'completed' => ProductionOrder::where('status', 'completed')->count(),
        ];
        
        return view('production-orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'currentStatus' => $request->status ?? 'all',
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Показать форму создания нового заказа
     */
    public function create()
    {
        $components = Detail::where('product_type_id', 2)
            ->orderBy('name')
            ->get();
        
        return view('production-orders.create', [
            'components' => $components,
        ]);
    }

    /**
     * Сохранить новый заказ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_ordered' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Проверить, что это Type 2 компонент
        $product = Detail::findOrFail($validated['product_id']);
        if ($product->product_type_id !== 2) {
            return back()->withErrors(['product_id' => 'Можно заказывать только компоненты Type 2']);
        }

        ProductionOrder::create($validated);

        return redirect()->route('production-orders.index')
            ->with('success', 'Заказ в производство создан');
    }

    /**
     * Показать детали заказа
     */
    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load('product');
        
        return view('production-orders.show', [
            'order' => $productionOrder,
        ]);
    }

    /**
     * Показать форму редактирования
     */
    public function edit(ProductionOrder $productionOrder)
    {
        $components = Detail::where('product_type_id', 2)
            ->orderBy('name')
            ->get();
        
        return view('production-orders.edit', [
            'order' => $productionOrder,
            'components' => $components,
        ]);
    }

    /**
     * Обновить заказ
     */
    public function update(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:ordering,in_production,ready,completed',
            'quantity_received' => 'required|integer|min:0|max:' . $productionOrder->quantity_ordered,
            'notes' => 'nullable|string',
        ]);

        $productionOrder->update($validated);

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('success', 'Заказ обновлен');
    }

    /**
     * Обновить статус заказа (AJAX)
     */
    public function updateStatus(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:ordering,in_production,ready,completed',
        ]);

        $productionOrder->update($validated);

        return response()->json([
            'success' => true,
            'status' => $productionOrder->status,
            'message' => 'Статус обновлен',
        ]);
    }

    /**
     * Зафиксировать приемку товара
     */
    public function receiveQuantity(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $productionOrder->getPendingQuantity(),
        ]);

        $productionOrder->receiveQuantity($validated['quantity']);

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('success', 'Товар принят в количестве ' . $validated['quantity'] . ' шт');
    }

    /**
     * Удалить заказ
     */
    public function destroy(ProductionOrder $productionOrder)
    {
        $productionOrder->delete();

        return redirect()->route('production-orders.index')
            ->with('success', 'Заказ удален');
    }
}
