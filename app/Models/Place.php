<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = ['place_id', 'name'];
    protected $table = 'places';

    public function cabinets()
    {
        return $this->belongsToMany(
            Cabinet::class,
            'cabinet_place',
            'place_detail_id',
            'cabinet_detail_id',
            'id',
            'id'
        );
    }

    public function stock()
    {
        return $this->hasOne(PlaceStock::class);
    }

    public function movements()
    {
        return $this->hasMany(PlaceStockMovement::class);
    }

    public function snapshots()
    {
        return $this->hasMany(PlaceStockSnapshot::class);
    }

    public function detail()
    {
        return $this->belongsTo(Detail::class, 'place_id', 'scu');
    }

    public function configuredIn()
    {
        // Кабинеты, в которых используется это место
        return $this->belongsToMany(
            Cabinet::class,
            'cabinet_place',
            'place_detail_id',
            'cabinet_detail_id',
            'id',
            'id'
        )
        ->using(CabinetPlace::class);
    }
}

