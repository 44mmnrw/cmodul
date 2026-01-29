<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_num',
        'order_date',
        'planned_date',
        'status',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'planned_date' => 'datetime',
    ];

    /**
     * Связь: Один заказ может иметь много производственных позиций
     */
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'reference_order');
    }
}
