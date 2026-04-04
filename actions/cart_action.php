<?php
session_start();
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $customer_id = $_SESSION['user_id'];
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];

    if($action == 'add') {
        $quantity = $_POST['quantity'] ?? 1;

        // Check if already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE customer_id=? AND product_id=?");
        $stmt->execute([$customer_id, $product_id]);
        $existing = $stmt->fetch();

        if($existing) {
            $new_qty = $existing['quantity'] + $quantity;
            $pdo->prepare("UPDATE cart SET quantity=? WHERE id=?")->execute([$new_qty, $existing['id']]);
        } else {
            $pdo->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)")->execute([$customer_id, $product_id, $quantity]);
        }
        $_SESSION['success'] = "Product added to cart.";
        header("Location: ../index.php?page=cart");
        exit;
    }
    elseif($action == 'update') {
        $quantity = $_POST['quantity'];
        if($quantity <= 0) {
            $pdo->prepare("DELETE FROM cart WHERE customer_id=? AND product_id=?")->execute([$customer_id, $product_id]);
        } else {
            $pdo->prepare("UPDATE cart SET quantity=? WHERE customer_id=? AND product_id=?")->execute([$quantity, $customer_id, $product_id]);
        }
        $_SESSION['success'] = "Cart updated.";
        header("Location: ../index.php?page=cart");
        exit;
    }
    elseif($action == 'remove') {
        $pdo->prepare("DELETE FROM cart WHERE customer_id=? AND product_id=?")->execute([$customer_id, $product_id]);
        $_SESSION['success'] = "Item removed from cart.";
        header("Location: ../index.php?page=cart");
        exit;
    }
}
?>
