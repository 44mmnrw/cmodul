<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceStockSnapshot extends Model
{
    protected $table = 'place_stock_snapshots';
    protected $fillable = ['place_id', 'snapshot_date', 'quantity'];
    protected $casts = [
        'snapshot_date' => 'date',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Создать снимок состояния на текущую дату
     */
    public static function createSnapshot($placeId, $date = null)
    {
        $date = $date ?: now()->date();
        $quantity = PlaceStock::getQuantity($placeId);

        return self::updateOrCreate(
            ['place_id' => $placeId, 'snapshot_date' => $date],
            ['quantity' => $quantity]
        );
    }

    /**
     * Получить остаток на конкретную дату
     */
    public static function getQuantityAtDate($placeId, $date)
    {
        $snapshot = self::where('place_id', $placeId)
            ->where('snapshot_date', '<=', $date)
            ->orderBy('snapshot_date', 'desc')
            ->first();

        return $snapshot?->quantity ?? 0;
    }
}
