<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $table = 'production_orders';
    
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'status_id',
        'planned_date',
        'notes',
        'reference_order',
    ];

    protected $casts = [
        'planned_date' => 'date',
    ];

    /**
     * Связь с заказом (Order)
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Компонент (Type 2), который заказан в производство
     */
    public function product()
    {
        return $this->belongsTo(Detail::class, 'product_id');
    }

    /**
     * Статус производственного заказа
     */
    public function orderStatus()
    {
        return $this->belongsTo(ProductionOrderStatus::class, 'status_id');
    }

    /**
     * Получить оставшееся количество на производстве
     */
    public function getQuantityPendingAttribute()
    {
        return $this->quantity_ordered - $this->quantity_received;
    }

    /**
     * Проверить, завершен ли заказ
     */
    public function isCompleted(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Отметить как полностью полученный
     */
    public function markAsCompleted()
    {
        $this->quantity_received = $this->quantity_ordered;
        $this->status = 'completed';
        return $this->save();
    }

    /**
     * Получить количество, которое еще ждет приемки
     */
    public function getPendingQuantity(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }

    /**
     * Зафиксировать приемку товара из производства
     */
    public function receiveQuantity(int $quantity, string $newStatus = 'ready'): bool
    {
        $this->quantity_received += $quantity;
        
        // Если получили всё, отмечаем как завершено
        if ($this->quantity_received >= $this->quantity_ordered) {
            $this->quantity_received = $this->quantity_ordered;
            $this->status = 'completed';
        } else {
            $this->status = $newStatus;
        }
        
        return $this->save();
    }


}
