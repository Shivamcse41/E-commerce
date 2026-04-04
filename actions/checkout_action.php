<?php
session_start();
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $customer_id = $_SESSION['user_id'];
    $total_amount = $_POST['total_amount'];
    $payment_method = $_POST['payment_method'];
    $shipping_address = $_POST['shipping_address'];

    try {
        $pdo->beginTransaction();

        // Check stock first
        $stmt = $pdo->prepare("SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id = ?");
        $stmt->execute([$customer_id]);
        $cart_items = $stmt->fetchAll();

        foreach($cart_items as $item) {
            if($item['quantity'] > $item['stock']) {
                throw new Exception("Product ID {$item['product_id']} is out of stock.");
            }
        }

        // Update user address if changed
        $pdo->prepare("UPDATE users SET address=? WHERE id=?")->execute([$shipping_address, $customer_id]);

        // Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$customer_id, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // Insert Order Items and Update Stock
        foreach($cart_items as $item) {
            // Get current price
            $stmt = $pdo->prepare("SELECT price, seller_id FROM products WHERE id=?");
            $stmt->execute([$item['product_id']]);
            $prod = $stmt->fetch();
            
            $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)")
                ->execute([$order_id, $item['product_id'], $item['quantity'], $prod['price']]);
            
            // Decrease stock
            $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id=?")
                ->execute([$item['quantity'], $item['product_id']]);
                
            // Notify seller
            $msg = "New order (#$order_id) placed for your product.";
            $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'seller')")
                ->execute([$prod['seller_id'], $msg]);
        }

        // Insert Payment
        $pdo->prepare("INSERT INTO payments (order_id, payment_method) VALUES (?, ?)")
            ->execute([$order_id, $payment_method]);

        // Clear Cart
        $pdo->prepare("DELETE FROM cart WHERE customer_id=?")->execute([$customer_id]);

        $pdo->commit();
        $_SESSION['success'] = "Order placed successfully! Order ID: #$order_id";
        header("Location: ../index.php?page=customer_orders");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error placing order: " . $e->getMessage();
        header("Location: ../index.php?page=checkout");
        exit;
    }
}
?>
