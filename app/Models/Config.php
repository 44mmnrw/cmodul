<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'configs';
    protected $fillable = ['master_id', 'slave_id', 'quantity'];
    public $timestamps = true;

    /**
     * Конфигурация (мастер продукт)
     */
    public function configuration()
    {
        return $this->belongsTo(Detail::class, 'master_id');
    }

    /**
     * Компонент (слейв продукт)
     */
    public function component()
    {
        return $this->belongsTo(Detail::class, 'slave_id');
    }
}
