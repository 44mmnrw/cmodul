<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceStockMovement extends Model
{
    protected $table = 'place_stock_movements';
    protected $fillable = ['place_id', 'type', 'quantity', 'reference', 'description', 'movement_date'];
    protected $casts = [
        'movement_date' => 'date',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Получить все приходы для места
     */
    public static function getIncoming($placeId, $fromDate = null, $toDate = null)
    {
        $query = self::where('place_id', $placeId)->where('type', 'incoming');
        
        if ($fromDate) {
            $query->whereDate('movement_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('movement_date', '<=', $toDate);
        }

        return $query->orderBy('movement_date', 'desc')->get();
    }

    /**
     * Получить все расходы для места
     */
    public static function getOutgoing($placeId, $fromDate = null, $toDate = null)
    {
        $query = self::where('place_id', $placeId)->where('type', 'outgoing');
        
        if ($fromDate) {
            $query->whereDate('movement_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('movement_date', '<=', $toDate);
        }

        return $query->orderBy('movement_date', 'desc')->get();
    }

    /**
     * Получить историю движений по месяцам
     */
    public static function getMonthlyStats($placeId, $year, $month)
    {
        $fromDate = "$year-$month-01";
        $toDate = date('Y-m-t', strtotime($fromDate));

        $incoming = self::getIncoming($placeId, $fromDate, $toDate)->sum('quantity');
        $outgoing = self::getOutgoing($placeId, $fromDate, $toDate)->sum('quantity');

        return [
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'balance' => $incoming - $outgoing,
        ];
    }
}
