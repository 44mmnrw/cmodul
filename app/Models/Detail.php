<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $table = 'products';

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
            'configs',
            'slave_id',
            'master_id',
            'id',
            'id'
        );
    }

    // Места, которые входят в конфигурацию этого кабинета
    public function componentsInConfiguration()
    {
        return $this->belongsToMany(
            Detail::class,
            'configs',
            'master_id',
            'slave_id',
            'id',
            'id'
        );
    }
}
