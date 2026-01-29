<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderStatus extends Model
{
    protected $table = 'production_order_statuses';

    protected $fillable = [
        'name',
        'color',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
