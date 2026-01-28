<?php

namespace App\Services;

use App\Models\Detail;
use App\Models\DesiredStockLevel;

/**
 * ForecastCalculationService
 * 
 * Сервис для расчета потребности в компонентах Type 2
 * на основе желаемых остатков Type 1 конфигураций
 * 
 * Пример использования:
 * $forecast = new ForecastCalculationService();
 * $result = $forecast->calculateComponentRequirements($configId);
 * 
 * Результат:
 * [
 *   'configuration' => Detail,
 *   'desired_quantity' => 50,
 *   'current_virtual_stock' => 40,
 *   'shortage' => 10,
 *   'components_needed' => [
 *     [
 *       'component' => Detail,
 *       'required_per_config' => 2,
 *       'needed_for_all_configs' => 100,
 *       'current_stock' => 95,
 *       'recommend_to_order' => 5,
 *       'is_bottleneck' => true,
 *     ],
 *     ...
 *   ]
 * ]
 */
class ForecastCalculationService
{
    /**
     * Рассчитать потребность в компонентах для достижения целевого остатка конфигурации
     * 
     * @param int|Detail $configurationIdOrModel - ID конфигурации Type 1 или её объект
     * @return array|null - Результат прогноза или null если конфигурация не найдена
     */
    public function calculateComponentRequirements($configurationIdOrModel): ?array
    {
        // Получаем конфигурацию
        if ($configurationIdOrModel instanceof Detail) {
            $configuration = $configurationIdOrModel;
        } else {
            $configuration = Detail::find($configurationIdOrModel);
        }

        if (!$configuration || $configuration->product_type_id != 1) {
            return null;
        }

        // Получаем целевой остаток конфигурации
        $desiredLevel = $configuration->desiredStockLevel;
        if (!$desiredLevel) {
            return null; // Целевой остаток не установлен
        }

        $desiredQuantity = $desiredLevel->desired_quantity;

        // Получаем текущий виртуальный остаток
        $virtualStock = $configuration->getVirtualStock();
        $currentQuantity = $virtualStock['quantity'] ?? 0;

        // Определяем дефицит
        $shortage = max(0, $desiredQuantity - $currentQuantity);

        // Получаем компоненты конфигурации
        $components = $configuration->componentsInConfiguration()
            ->with('stock')
            ->get();

        $componentsNeeded = [];
        $bottleneckComponentId = $virtualStock['limiting_component_id'] ?? null;

        foreach ($components as $component) {
            $requiredPerConfig = $component->pivot->quantity ?? 1;
            $neededForAllConfigs = $desiredQuantity * $requiredPerConfig;
            
            $currentStock = $component->stock?->available ?? 0;
            $recommendToOrder = max(0, $neededForAllConfigs - $currentStock);

            $componentsNeeded[] = [
                'component_id' => $component->id,
                'component' => $component,
                'component_name' => $component->name,
                'required_per_config' => $requiredPerConfig,
                'needed_for_all_configs' => $neededForAllConfigs,
                'current_stock' => $currentStock,
                'recommend_to_order' => $recommendToOrder,
                'is_bottleneck' => $component->id === $bottleneckComponentId,
            ];
        }

        return [
            'configuration_id' => $configuration->id,
            'configuration_name' => $configuration->name,
            'configuration' => $configuration,
            'desired_quantity' => $desiredQuantity,
            'current_virtual_stock' => $currentQuantity,
            'shortage' => $shortage,
            'total_components_to_order' => array_sum(array_column($componentsNeeded, 'recommend_to_order')),
            'components_needed' => $componentsNeeded,
            'calculated_at' => now(),
        ];
    }

    /**
     * Рассчитать потребность для всех конфигураций с установленными целевыми остатками
     * 
     * @return array - Массив результатов прогноза для каждой конфигурации
     */
    public function calculateAllComponentRequirements(): array
    {
        $configurations = DesiredStockLevel::with('configuration')
            ->get()
            ->filter(fn($level) => $level->configuration && $level->configuration->product_type_id == 1)
            ->pluck('configuration');

        $forecasts = [];

        foreach ($configurations as $config) {
            $forecast = $this->calculateComponentRequirements($config);
            if ($forecast) {
                $forecasts[] = $forecast;
            }
        }

        return $forecasts;
    }

    /**
     * Получить сводку по всем необходимым заказам компонентов
     * (объединенная потребность по всем конфигурациям)
     * 
     * @return array - Сводка по компонентам
     */
    public function getOrderSummary(): array
    {
        $allForecasts = $this->calculateAllComponentRequirements();
        $orderSummary = [];

        foreach ($allForecasts as $forecast) {
            foreach ($forecast['components_needed'] as $needed) {
                $componentId = $needed['component_id'];

                if (!isset($orderSummary[$componentId])) {
                    $orderSummary[$componentId] = [
                        'component_id' => $componentId,
                        'component_name' => $needed['component_name'],
                        'component' => $needed['component'],
                        'total_recommend_to_order' => 0,
                        'used_by_configurations' => [],
                    ];
                }

                $orderSummary[$componentId]['total_recommend_to_order'] += $needed['recommend_to_order'];
                $orderSummary[$componentId]['used_by_configurations'][] = [
                    'configuration_name' => $forecast['configuration_name'],
                    'required_quantity' => $needed['recommend_to_order'],
                ];
            }
        }

        return array_values($orderSummary);
    }
}
