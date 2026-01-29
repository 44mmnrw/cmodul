<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_num',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Связь: Один заказ может иметь много производственных позиций
     */
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'order_id');
    }
}
