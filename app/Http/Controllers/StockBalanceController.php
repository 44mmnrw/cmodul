<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\ProductType;
use Illuminate\Http\Request;

class StockBalanceController extends Controller
{
    /**
     * Показать страницу виртуальных остатков
     */
    public function virtualStock(Request $request)
    {
        try {
            // Получить все конфигурации Type 1
            $configurations = Detail::where('product_type_id', 1)
                ->get();

            // Рассчитать виртуальные остатки для каждой конфигурации
            $data = [];
            foreach ($configurations as $config) {
                $virtualStock = $config->getVirtualStock() ?? [];
                
                $data[] = [
                    'id' => $config->id,
                    'name' => $config->name ?? 'Неизвестно',
                    'cabinet_id' => $config->cabinet_id ?? 'N/A',
                    'scu' => $config->scu ?? 'N/A',
                    'description' => $config->description ?? '',
                    'virtual_stock' => $virtualStock['quantity'] ?? 0,
                    'limiting_component_id' => $virtualStock['limiting_component_id'] ?? null,
                    'limiting_component' => $virtualStock['limiting_component']?->name ?? 'N/A',
                    'limiting_available' => $virtualStock['limiting_component']?->stock?->available ?? 0,
                    'limiting_required' => $virtualStock['limiting_required'] ?? 1,
                    'unit_price' => 0,
                    'total_price' => 0,
                    'status' => $this->getStockStatus($virtualStock['quantity'] ?? 0),
                    'status_class' => $this->getStatusClass($virtualStock['quantity'] ?? 0),
                ];
            }

            // Фильтрация по поиску
            if ($search = $request->get('search')) {
                $data = array_filter($data, function ($item) use ($search) {
                    return stripos($item['name'], $search) !== false ||
                           stripos($item['cabinet_id'], $search) !== false ||
                           stripos($item['description'], $search) !== false;
                });
            }

            // Статистика
            $total_price = 0;
            foreach ($data as $item) {
                $total_price += $item['total_price'];
            }

            $low_stock = 0;
            $out_of_stock = 0;
            foreach ($data as $item) {
                if ($item['virtual_stock'] > 0 && $item['virtual_stock'] <= 5) {
                    $low_stock++;
                }
                if ($item['virtual_stock'] == 0) {
                    $out_of_stock++;
                }
            }

            $stats = [
                'total_configs' => count($configurations),
                'total_cost' => $total_price,
                'low_stock' => $low_stock,
                'out_of_stock' => $out_of_stock,
            ];

            return view('stock.virtual-stock', [
                'configurations' => array_values($data),
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
     * Получить текстовый статус остатка
     */
    private function getStockStatus($quantity)
    {
        if ($quantity == 0) {
            return 'Нет в наличии';
        } elseif ($quantity <= 5) {
            return 'Низкий запас';
        } else {
            return 'В наличии';
        }
    }

    /**
     * Получить CSS класс для статуса
     */
    private function getStatusClass($quantity)
    {
        if ($quantity == 0) {
            return 'danger';
        } elseif ($quantity <= 5) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    /**
     * API endpoint для получения виртуальных остатков в JSON
     */
    public function apiVirtualStock()
    {
        $configurations = Detail::where('product_type_id', 1)
            ->with(['componentsInConfiguration.stock', 'stock'])
            ->get()
            ->map(function ($config) {
                $virtualStock = $config->getVirtualStock();
                return [
                    'id' => $config->id,
                    'name' => $config->name,
                    'virtual_stock' => $virtualStock['quantity'] ?? 0,
                    'limiting_component' => $virtualStock['limiting_component']?->name,
                    'status' => $this->getStockStatus($virtualStock['quantity'] ?? 0),
                ];
            });

        return response()->json($configurations);
    }
}
