<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cabinet model - represents finished products (cabinets)
 * Mapped to Detail model with product_type_id = 1
 * 
 * @deprecated Use Detail model directly with product_type_id = 1
 */
class Cabinet extends Model
{
    protected $fillable = ['cabinet_id', 'name'];
    protected $table = 'products';

    public function places()
    {
        return $this->belongsToMany(
            Place::class,
            'configs',
            'master_id',
            'slave_id',
            'id',
            'id'
        );
    }

    public function detail()
    {
        return $this->belongsTo(Detail::class, 'cabinet_id', 'scu');
    }
}


