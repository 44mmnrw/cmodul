<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceStock extends Model
{
    protected $fillable = ['place_id', 'quantity', 'last_updated_at'];
    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function movements()
    {
        return $this->place->movements();
    }

    /**
     * Получить текущий остаток
     */
    public static function getQuantity($placeId)
    {
        return self::where('place_id', $placeId)->first()?->quantity ?? 0;
    }

    /**
     * Увеличить остаток (приход)
     */
    public static function addStock($placeId, $quantity, $reference = null, $description = null)
    {
        $stock = self::firstOrCreate(['place_id' => $placeId]);
        $stock->quantity += $quantity;
        $stock->last_updated_at = now();
        $stock->save();

        // Создаём запись о движении
        PlaceStockMovement::create([
            'place_id' => $placeId,
            'type' => 'incoming',
            'quantity' => $quantity,
            'reference' => $reference,
            'description' => $description,
            'movement_date' => now()->date(),
        ]);

        return $stock;
    }

    /**
     * Уменьшить остаток (расход)
     */
    public static function removeStock($placeId, $quantity, $reference = null, $description = null)
    {
        $stock = self::firstOrCreate(['place_id' => $placeId]);
        $stock->quantity = max(0, $stock->quantity - $quantity);
        $stock->last_updated_at = now();
        $stock->save();

        // Создаём запись о движении
        PlaceStockMovement::create([
            'place_id' => $placeId,
            'type' => 'outgoing',
            'quantity' => $quantity,
            'reference' => $reference,
            'description' => $description,
            'movement_date' => now()->date(),
        ]);

        return $stock;
    }

    /**
     * Пересчитать остаток из истории движений
     */
    public static function recalculateFromMovements($placeId)
    {
        $totalIncoming = PlaceStockMovement::where('place_id', $placeId)
            ->where('type', 'incoming')
            ->sum('quantity');
        
        $totalOutgoing = PlaceStockMovement::where('place_id', $placeId)
            ->where('type', 'outgoing')
            ->sum('quantity');
        
        $quantity = $totalIncoming - $totalOutgoing;

        return self::updateOrCreate(
            ['place_id' => $placeId],
            [
                'quantity' => max(0, $quantity),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Пересчитать остатки для всех мест
     */
    public static function recalculateAll()
    {
        $places = \App\Models\Place::all();
        foreach ($places as $place) {
            self::recalculateFromMovements($place->id);
        }
    }
}
