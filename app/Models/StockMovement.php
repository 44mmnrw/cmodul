<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'reason',
        'document_number',
        'balance_before',
        'balance_after'
    ];

    public function product()
    {
        return $this->belongsTo(Detail::class, 'product_id');
    }
}
