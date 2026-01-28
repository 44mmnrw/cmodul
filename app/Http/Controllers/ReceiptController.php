<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    /**
     * Показать страницу приходов с формой
     */
    public function index()
    {
        $components = Detail::where('product_type_id', 2)
            ->with('stock')
            ->get();

        return view('receipts.index', [
            'components' => $components
        ]);
    }

    /**
     * Показать страницу создания прихода
     */
    public function create()
    {
        $components = Detail::where('product_type_id', 2)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('receipts.create', [
            'components' => $components
        ]);
    }

    /**
     * Сохранить приход товара
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.date' => 'required|date',
            'items.*.reason' => 'nullable|string'
        ]);

        try {
            $savedCount = 0;
            
            // Генерировать номер документа
            $year = date('Y');
            $lastNumber = StockMovement::where('movement_type', 'IN')
                ->whereYear('created_at', $year)
                ->count();
            $documentNumber = 'ПР-' . $year . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

            foreach ($validated['items'] as $item) {
                // Получить или создать запись остатка
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item['product_id']],
                    ['quantity' => 0, 'reserved' => 0, 'min_quantity' => 0]
                );

                $balanceBefore = $stock->quantity;
                
                // Добавить товар
                $stock->increment('quantity', $item['quantity']);

                // Логировать операцию
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'movement_type' => 'IN',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'Receipt',
                    'reason' => $item['reason'] ?? null,
                    'document_number' => $documentNumber,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $stock->quantity
                ]);

                $savedCount++;
            }

            return redirect()->route('receipts.index')
                ->with('success', "Приход зарегистрирован! Добавлено {$savedCount} позиций.");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при сохранении прихода: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Показать историю приходов
     */
    public function history()
    {
        $movements = StockMovement::where('movement_type', 'IN')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Статистика
        $totalOperations = StockMovement::where('movement_type', 'IN')->count();
        $totalQuantity = StockMovement::where('movement_type', 'IN')->sum('quantity');
        $uniqueComponentsCount = StockMovement::where('movement_type', 'IN')
            ->distinct('product_id')
            ->count('product_id');

        // Сводка по компонентам
        $componentSummary = StockMovement::where('movement_type', 'IN')
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity')
                ];
            })
            ->values()
            ->toArray();

        return view('receipts.history', [
            'movements' => $movements,
            'totalOperations' => $totalOperations,
            'totalQuantity' => $totalQuantity,
            'uniqueComponentsCount' => $uniqueComponentsCount,
            'componentSummary' => $componentSummary
        ]);
    }

    /**
     * Показать журнал приходов (документы)
     */
    public function journal()
    {
        // Получить все приходы, сгруппированные по документам
        $movements = StockMovement::where('movement_type', 'IN')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();

        // Статистика
        $totalOperations = $movements->count();
        $totalQuantity = $movements->sum('quantity');
        $uniqueComponentsCount = $movements->groupBy('product_id')->count();

        // Сгруппировать по date (считать документом)
        $receipts = collect($movements->groupBy(function ($movement) {
            return $movement->created_at->format('Y-m-d');
        })->map(function ($group, $date) {
            $firstItem = $group->first();
            return [
                'id' => $firstItem->id,
                'number' => $firstItem->document_number,
                'date' => Carbon::createFromFormat('Y-m-d', $date)->format('d.m.Y'),
                'items_count' => $group->count(),
                'total_quantity' => $group->sum('quantity'),
                'notes' => $firstItem->reason ?? '—'
            ];
        })->values());

        // Пагинация (10 документов на странице)
        $page = request('page', 1);
        $perPage = 10;
        $receiptsPage = $receipts->slice(($page - 1) * $perPage, $perPage)->values();

        return view('receipts.journal', [
            'receipts' => $receiptsPage,
            'totalOperations' => $totalOperations,
            'totalQuantity' => $totalQuantity,
            'uniqueComponentsCount' => $uniqueComponentsCount
        ]);
    }

    /**
     * Показать детали конкретного прихода
     */
    public function show($id)
    {
        // Получить движение по ID
        $movement = StockMovement::find($id);

        if (!$movement || $movement->movement_type !== 'IN') {
            abort(404, 'Приход не найден');
        }

        // Дата этого движения - это дата документа
        $documentDate = $movement->created_at->format('Y-m-d');

        // Получить все движения в этот день
        $movements = StockMovement::where('movement_type', 'IN')
            ->whereDate('created_at', $documentDate)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Статистика по этому приходу
        $totalOperations = $movements->total();
        $totalQuantity = $movements->sum('quantity');
        $uniqueComponentsCount = $movements->groupBy('product_id')->count();

        // Сводка по компонентам
        $allMovementsForSummary = StockMovement::where('movement_type', 'IN')
            ->whereDate('created_at', $documentDate)
            ->with('product')
            ->get();

        $componentSummary = $allMovementsForSummary
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity')
                ];
            })
            ->values()
            ->toArray();

        return view('receipts.history', [
            'movements' => $movements,
            'totalOperations' => $totalOperations,
            'totalQuantity' => $totalQuantity,
            'uniqueComponentsCount' => $uniqueComponentsCount,
            'componentSummary' => $componentSummary,
            'isDetail' => true,
            'backUrl' => route('receipts.journal')
        ]);
    }
}
