<?php
require_once 'config.php';

// Oturum kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login-register.html');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Siparişleri getir
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(
                   CONCAT(p.name, '|', p.image, '|', oi.quantity, '|', oi.unit_price)
                   SEPARATOR '||'
               ) as order_items
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ");
    
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Siparişleri düzenle
    $formatted_orders = [];
    foreach ($orders as $order) {
        $items = [];
        if ($order['order_items']) {
            $order_items = explode('||', $order['order_items']);
            foreach ($order_items as $item) {
                list($name, $image, $quantity, $price) = explode('|', $item);
                $items[] = [
                    'name' => $name,
                    'image' => $image,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
        }

        $formatted_orders[] = [
            'order_id' => $order['order_id'],
            'order_date' => date('d.m.Y H:i', strtotime($order['order_date'])),
            'total_amount' => number_format($order['total_amount'], 2, ',', '.') . ' TL',
            'status' => $order['status'],
            'items' => $items
        ];
    }

    echo json_encode(['success' => true, 'orders' => $formatted_orders]);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Siparişler getirilirken bir hata oluştu.']);
}
?> 