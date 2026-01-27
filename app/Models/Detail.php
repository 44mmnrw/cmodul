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

    public function places()
    {
        return $this->belongsToMany(
            Place::class, 
            'configs',
            'master_id',     // foreign key on configs table pointing to products (master)
            'slave_id',      // foreign key on configs table pointing to products (slave/place)
            'id',            // local key on products table
            'id'             // local key on products table
        )
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
