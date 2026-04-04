<?php
session_start();
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])){
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    if($_SESSION['role'] != 'seller' && $_SESSION['role'] != 'admin') {
        $_SESSION['error'] = "Unauthorized action.";
        header("Location: ../index.php");
        exit;
    }

    try {
        $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $order_id]);
        
        // Notify Customer
        $stmt = $pdo->prepare("SELECT customer_id FROM orders WHERE id=?");
        $stmt->execute([$order_id]);
        $customer_id = $stmt->fetchColumn();

        $msg = "Your order (#$order_id) status has been updated to: $status.";
        $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'customer')")
            ->execute([$customer_id, $msg]);

        $_SESSION['success'] = "Order status updated.";
        $redirect = $_SESSION['role'] == 'admin' ? 'admin_orders' : 'seller_orders';
        header("Location: ../index.php?page=$redirect");
        exit;
    } catch(Exception $e) {
        $_SESSION['error'] = "Error updating order: " . $e->getMessage();
        $redirect = $_SESSION['role'] == 'admin' ? 'admin_orders' : 'seller_orders';
        header("Location: ../index.php?page=$redirect");
        exit;
    }
}
?>
