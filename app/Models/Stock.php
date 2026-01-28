<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';
    protected $fillable = ['product_id', 'quantity', 'reserved', 'min_quantity'];
    protected $appends = ['available'];

    public function product()
    {
        return $this->belongsTo(Detail::class, 'product_id');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }

    public function getAvailableAttribute()
    {
        return $this->quantity - $this->reserved;
    }
}
