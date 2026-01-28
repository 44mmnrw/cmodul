<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    /**
     * Показать журнал отгрузок
     * Загружает реальные отгрузки из stock_movements
     */
    public function journal(Request $request)
    {
        try {
            // Получить все записи об отгрузках (OUT) из stock_movements
            $movementsQuery = StockMovement::where('movement_type', 'OUT')
                ->with('product')
                ->orderBy('created_at', 'desc');
            
            // Фильтр по периоду
            $period = $request->get('period', 'all');
            if ($period === 'today') {
                $movementsQuery->whereDate('created_at', now()->toDateString());
            } elseif ($period === 'week') {
                $movementsQuery->whereBetween('created_at', [
                    now()->subDays(7),
                    now()
                ]);
            } elseif ($period === 'month') {
                $movementsQuery->whereBetween('created_at', [
                    now()->subDays(30),
                    now()
                ]);
            }
            
            // Поиск по наименованию причины
            if ($request->has('search')) {
                $search = $request->get('search');
                $movementsQuery->where('reason', 'like', "%$search%");
            }
            
            $movements = $movementsQuery->get();
            
            // Группировать движения по отгрузкам (по дате и причине)
            $shipments = $movements->groupBy(function ($movement) {
                return $movement->created_at->format('Y-m-d H:i');
            })->map(function ($group, $dateTime) {
                $firstMovement = $group->first();
                $totalQuantity = $group->sum('quantity');
                $totalAmount = 0;
                
                // Подробные детали отгрузки
                $items = [];
                foreach ($group as $movement) {
                    $itemSum = $movement->quantity * ($movement->product->weight ?? 0);
                    $totalAmount += $itemSum;
                    
                    $items[] = [
                        'name' => $movement->product->name ?? 'Неизвестный компонент',
                        'code' => $movement->product->scu ?? 'N/A',
                        'quantity' => $movement->quantity,
                        'unit_price' => $movement->product->weight ?? 0,
                        'sum' => $itemSum,
                    ];
                }
                
                return [
                    'id' => $firstMovement->id,
                    'number' => 'ОТ-' . $firstMovement->created_at->format('Y-m-d'),
                    'date' => $firstMovement->created_at->format('d.m.Y'),
                    'time' => $firstMovement->created_at->format('H:i'),
                    'client' => $firstMovement->client ?? 'Неизвестный клиент',
                    'address' => $firstMovement->address ?? '',
                    'quantity' => $totalQuantity,
                    'amount' => $totalAmount,
                    'items_count' => $group->count(),
                    'items' => $items,  // Детали отгрузки
                ];
            })->values();
            
            // Статистика
            $stats = [
                'total_shipments' => $movements->groupBy(function ($m) {
                    return $m->created_at->format('Y-m-d H:i');
                })->count(),
                'total_quantity' => $movements->sum('quantity'),
                'total_cost' => $movements->sum(function ($m) {
                    return $m->quantity * ($m->product->weight ?? 0);
                }),
                'last_shipment' => $movements->first()?->created_at?->format('d.m.Y') ?? '—',
            ];

            return view('shipments.journal', [
                'shipments' => $shipments,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Показать форму создания отгрузки
     */
    public function create()
    {
        // Загружаем готовые изделия (product_type_id = 1) из таблицы products
        $configurations = Detail::where('product_type_id', 1)
            ->select('id', 'name')
            ->get()
            ->map(function ($item) {
                // Рассчитываем виртуальный остаток
                $virtualStock = $this->calculateVirtualStock($item);
                
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'virtual_stock' => $virtualStock,
                ];
            })
            ->toArray();
        
        return view('shipments.form', [
            'configurations' => $configurations,
        ]);
    }
    
    /**
     * Рассчитать виртуальный остаток конфигурации (Type 1)
     * на основе доступных компонентов (Type 2)
     */
    private function calculateVirtualStock(Detail $config)
    {
        // Получить все компоненты конфигурации
        $components = DB::table('configs')
            ->where('master_id', $config->id)
            ->get();
        
        if ($components->isEmpty()) {
            return 0;
        }
        
        $minAvailable = PHP_INT_MAX;
        
        foreach ($components as $componentConfig) {
            $componentId = $componentConfig->slave_id;
            $requiredQty = $componentConfig->quantity;
            
            // Получить остатки компонента
            $stock = DB::table('stocks')
                ->where('product_id', $componentId)
                ->first();
            
            if (!$stock) {
                return 0;  // Нет остатков - не можем собрать
            }
            
            $available = $stock->quantity;
            $canMake = floor($available / $requiredQty);
            $minAvailable = min($minAvailable, $canMake);
        }
        
        return $minAvailable === PHP_INT_MAX ? 0 : $minAvailable;
    }

    /**
     * Сохранить новую отгрузку
     * Списывает компоненты (Type 2) через таблицу configs
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client' => 'required|string|max:255',
                'shipment_date' => 'required|date',
                'address' => 'nullable|string|max:500',
                'items' => 'required|array|min:1',
                'items.*.config_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            // Используем транзакцию для атомарности операции
            DB::transaction(function () use ($validated) {
                // Для каждого отгружаемого шкафа (Type 1)
                foreach ($validated['items'] as $item) {
                    $configId = $item['config_id'];
                    $shipmentQty = $item['quantity'];
                    
                    // Получить конфигурацию (Type 1)
                    $config = Detail::find($configId);
                    
                    if (!$config || $config->product_type_id != 1) {
                        throw new \Exception("Конфигурация $configId не найдена или неверный тип");
                    }
                    
                    // Получить компоненты (Type 2) через таблицу configs
                    $components = DB::table('configs')
                        ->where('master_id', $configId)
                        ->get();
                    
                    // Для каждого компонента списать его из остатков
                    foreach ($components as $componentConfig) {
                        $componentId = $componentConfig->slave_id;
                        $componentQtyPerConfig = $componentConfig->quantity;
                        
                        // Общее количество этого компонента для списания
                        $totalToRemove = $componentQtyPerConfig * $shipmentQty;
                        
                        // Получить текущий остаток
                        $stock = DB::table('stocks')
                            ->where('product_id', $componentId)
                            ->first();
                        
                        if (!$stock) {
                            throw new \Exception("Остатки для компонента $componentId не найдены");
                        }
                        
                        $balanceBefore = $stock->quantity;
                        $balanceAfter = $balanceBefore - $totalToRemove;
                        
                        if ($balanceAfter < 0) {
                            $component = Detail::find($componentId);
                            throw new \Exception("Недостаточно остатков для {$component->name}");
                        }
                        
                        // Создать запись о движении (OUT)
                        StockMovement::create([
                            'product_id' => $componentId,
                            'movement_type' => 'OUT',
                            'quantity' => $totalToRemove,
                            'reference_type' => 'Shipment',
                            'reference_id' => null,
                            'reason' => "Отгрузка шкафа {$config->name}",
                            'client' => $validated['client'],
                            'address' => $validated['address'] ?? '',
                            'notes' => $validated['notes'] ?? '',
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                        ]);
                        
                        // Обновить остатки (уменьшить quantity)
                        DB::table('stocks')
                            ->where('product_id', $componentId)
                            ->decrement('quantity', $totalToRemove);
                    }
                }
            });

            return redirect()->route('shipments.journal')
                ->with('success', 'Отгрузка создана успешно и компоненты списаны со склада');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Показать детали отгрузки
     */
    public function show($id)
    {
        // TODO: Реализовать просмотр деталей отгрузки
        
        return view('shipments.show', [
            'shipment' => null,
        ]);
    }
}
