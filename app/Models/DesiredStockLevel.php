<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesiredStockLevel extends Model
{
    protected $fillable = [
        'product_id',
        'desired_quantity',
    ];

    protected $casts = [
        'desired_quantity' => 'integer',
    ];

    /**
     * Конфигурация (Type 1), для которой установлен целевой остаток
     */
    public function configuration()
    {
        return $this->belongsTo(Detail::class, 'product_id');
    }
}
