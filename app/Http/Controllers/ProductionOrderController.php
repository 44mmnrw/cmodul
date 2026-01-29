<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Order;
use App\Models\Detail;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductionOrder::with('order', 'orderStatus')
            ->selectRaw('order_id, status_id, MIN(id) as id, MIN(created_at) as created_at, MIN(planned_date) as planned_date, COUNT(*) as config_count, SUM(quantity_ordered) as total_quantity, MIN(notes) as notes')
            ->groupBy('order_id', 'status_id');
        
        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $statusNames = [
                    'ordering' => 'Ожидает',
                    'in_production' => 'В производстве',
                    'ready' => 'Готов',
                    'completed' => 'Завершен',
                ];
                $statusName = $statusNames[$request->status] ?? $request->status;
                $q->where('name', $statusName);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('notes', 'like', "%$search%")
                  ->orWhereHas('order', function ($q) use ($search) {
                      $q->where('order_num', 'like', "%$search%");
                  });
        }
        
        $orders = $query->orderByDesc('created_at')->paginate(10);
        
        $stats = [
            'total' => ProductionOrder::count(),
            'ordering' => ProductionOrder::whereHas('orderStatus', function ($q) {
                $q->where('name', 'Ожидает');
            })->count(),
            'in_production' => ProductionOrder::whereHas('orderStatus', function ($q) {
                $q->where('name', 'В производстве');
            })->count(),
            'ready' => ProductionOrder::whereHas('orderStatus', function ($q) {
                $q->where('name', 'Готов');
            })->count(),
            'completed' => ProductionOrder::whereHas('orderStatus', function ($q) {
                $q->where('name', 'Завершен');
            })->count(),
        ];
        
        return view('production-orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'currentStatus' => $request->status ?? 'all',
            'search' => $request->search ?? '',
        ]);
    }

    public function create()
    {
        $components = Detail::where('product_type_id', 1)
            ->orderBy('name')
            ->get();
        
        $statuses = \App\Models\ProductionOrderStatus::orderBy('sort_order')->get();
        
        $orders = null;
        if (request()->filled('edit')) {
            $firstOrder = ProductionOrder::with('product', 'orderStatus', 'order')->find(request('edit'));
            if ($firstOrder) {
                $orders = ProductionOrder::where('order_id', $firstOrder->order_id)
                    ->with('product', 'orderStatus', 'order')
                    ->orderBy('id')
                    ->get();
            }
        }
        
        return view('production-orders.create', [
            'components' => $components,
            'statuses' => $statuses,
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $isEditing = $request->filled('edit_id');
        
        $validated = $request->validate([
            'order_date' => 'nullable|date',
            'status_id' => 'nullable|exists:production_order_statuses,id',
            'configs' => 'required|array|min:1',
            'configs.*.product_id' => 'required|exists:products,id',
            'configs.*.quantity' => 'required|integer|min:1',
            'configs.*.planned_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $orderId = null;
        if ($isEditing) {
            $existingOrder = ProductionOrder::find($request->input('edit_id'));
            $orderId = $existingOrder->order_id;
            ProductionOrder::where('order_id', $orderId)->delete();
        } else {
            $orderNum = self::generateOrderNum();
            $order = Order::create([
                'order_num' => $orderNum,
                'date' => now(),
            ]);
            $orderId = $order->id;
        }
        
        $statusId = $validated['status_id'] ?? \App\Models\ProductionOrderStatus::where('name', 'Ожидает')->first()?->id;

        foreach ($validated['configs'] as $config) {
            $product = Detail::findOrFail($config['product_id']);
            if ($product->product_type_id !== 1) {
                return back()->withErrors(['configs' => 'Можно заказывать только конфигурации Type 1']);
            }

            ProductionOrder::create([
                'order_id' => $orderId,
                'product_id' => $config['product_id'],
                'quantity_ordered' => $config['quantity'],
                'planned_date' => $config['planned_date'],
                'notes' => $validated['notes'] ?? null,
                'status_id' => $statusId,
            ]);
        }

        $message = $isEditing ? 'Заказ обновлен' : 'Заказ в производство создан';
        return redirect()->route('production-orders.index')
            ->with('success', $message);
    }

    private static function generateOrderNum()
    {
        $lastOrder = Order::latest('id')->first();
        $nextNumber = ($lastOrder ? $lastOrder->id : 0) + 1;
        return 'ПО-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function show(ProductionOrder $productionOrder)
    {
        $orders = ProductionOrder::where('order_id', $productionOrder->order_id)
            ->with('product', 'orderStatus', 'order')
            ->orderBy('id')
            ->get();
        
        return view('production-orders.show', [
            'orders' => $orders,
        ]);
    }

    public function edit(ProductionOrder $productionOrder)
    {
        return redirect()->route('production-orders.create', ['edit' => $productionOrder->id]);
    }

    public function update(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:production_order_statuses,id',
            'quantity_received' => 'required|integer|min:0|max:' . $productionOrder->quantity_ordered,
            'notes' => 'nullable|string',
        ]);

        ProductionOrder::where('order_id', $productionOrder->order_id)
            ->update([
                'status_id' => $validated['status_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

        $productionOrder->update(['quantity_received' => $validated['quantity_received']]);

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('success', 'Заказ обновлен');
    }

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

    public function receiveQuantity(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $productionOrder->getPendingQuantity(),
        ]);

        $productionOrder->receiveQuantity($validated['quantity']);

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('success', 'Товар принят в количестве ' . $validated['quantity'] . ' шт');
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $orderId = $productionOrder->order_id;
        ProductionOrder::where('order_id', $orderId)->delete();

        return redirect()->route('production-orders.index')
            ->with('success', 'Заказ удален');
    }
}
