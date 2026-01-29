SELECT 
    po.id,
    po.order_id,
    o.order_num,
    po.product_id,
    p.name as product_name,
    p.product_type_id,
    po.quantity_ordered,
    po.reference_order,
    po.planned_date
FROM production_orders po
LEFT JOIN orders o ON po.order_id = o.id
LEFT JOIN products p ON po.product_id = p.id
ORDER BY po.id DESC
LIMIT 15;
