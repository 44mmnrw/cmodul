<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'weight',
        'height',
        'width',
        'depth',
        'scu',
        'description',
        'category_id',
        'source_id',
        'material',
        'product_type_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    // Кабинеты, в которых это изделие используется (как место в конфигурации)
    public function usedInCabinets()
    {
        return $this->belongsToMany(
            Detail::class,
            'configs',
            'slave_id',
            'master_id',
            'id',
            'id'
        );
    }

    // Места, которые входят в конфигурацию этого кабинета
    public function componentsInConfiguration()
    {
        return $this->belongsToMany(
            Detail::class,
            'configs',
            'master_id',
            'slave_id',
            'id',
            'id'
        );
    }

    // Остатки товара на складе (только для Type 2)
    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_id', 'id');
    }

    // Целевой остаток конфигурации (только для Type 1)
    public function desiredStockLevel()
    {
        return $this->hasOne(DesiredStockLevel::class, 'product_id', 'id');
    }

    // Заказы в производство для этого компонента (Type 2)
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'product_id', 'id');
    }

    /**
     * Рассчитать виртуальный остаток конфигурации (Type 1)
     * Сколько шкафов можно собрать из имеющихся компонентов
     * 
     * @return array ['quantity' => int, 'limiting_component_id' => int, 'limiting_component' => Detail]
     */
    public function getVirtualStock()
    {
        // Работает только для Type 1
        if ($this->product_type_id != 1) {
            return null;
        }

        $components = $this->componentsInConfiguration()
            ->with('stock')
            ->get();

        if ($components->isEmpty()) {
            return ['quantity' => 0, 'limiting_component_id' => null];
        }

        $minAvailable = PHP_INT_MAX;
        $limitingComponent = null;

        foreach ($components as $component) {
            // Компонент должен быть Type 2 и иметь остатки
            if ($component->product_type_id != 2 || !$component->stock) {
                return ['quantity' => 0, 'limiting_component_id' => $component->id];
            }

            $requiredQty = $component->pivot->quantity ?? 1;
            if ($requiredQty <= 0) {
                return ['quantity' => 0, 'limiting_component_id' => $component->id];
            }
            
            $availableQty = $component->stock->available ?? 0;
            
            // Сколько конфигураций можем собрать с этим компонентом?
            $canMake = floor($availableQty / $requiredQty);
            
            if ($canMake < $minAvailable) {
                $minAvailable = $canMake;
                $limitingComponent = $component;
            }
        }

        return [
            'quantity' => $minAvailable === PHP_INT_MAX ? 0 : $minAvailable,
            'limiting_component_id' => $limitingComponent?->id,
            'limiting_component' => $limitingComponent,
            'limiting_required' => $limitingComponent?->pivot->quantity,
        ];
    }
}
