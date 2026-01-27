<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
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
            'cabinet_place',
            'place_detail_id',
            'cabinet_detail_id',
            'id',
            'id'
        );
    }

    // Места, которые входят в конфигурацию этого кабинета
    public function componentsInConfiguration()
    {
        return $this->belongsToMany(
            Detail::class,
            'cabinet_place',
            'cabinet_detail_id',
            'place_detail_id',
            'id',
            'id'
        );
    }

    public function places()
    {
        return $this->belongsToMany(
            Place::class, 
            'place_detail',
            'detail_id',     // foreign key on place_detail table pointing to details
            'place_id',      // foreign key on place_detail table pointing to places
            'id',            // local key on details table
            'place_id'       // local key on places table (it's the place_id column, not id!)
        )
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
