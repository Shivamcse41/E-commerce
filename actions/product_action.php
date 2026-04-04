<?php
session_start();
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['action'];
    $seller_id = $_SESSION['user_id'];
    
    // Auth Check
    if($_SESSION['role'] != 'seller' && $_SESSION['role'] != 'admin') {
        $_SESSION['error'] = "Unauthorized action.";
        header("Location: ../index.php");
        exit;
    }

    if($action == 'add') {
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];
        $product_image = 'default_product.png';

        // Image Upload
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['product_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array(strtolower($ext), $allowed)) {
                $new_filename = time() . '_' . basename($filename);
                move_uploaded_file($_FILES['product_image']['tmp_name'], "../uploads/" . $new_filename);
                $product_image = $new_filename;
            }
        } elseif(!empty($_POST['image_url'])) {
            $url = $_POST['image_url'];
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $new_filename = time() . '_remote.' . $ext;
            
            $options = ["http" => ["header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"]];
            $context = stream_context_create($options);
            $img_data = @file_get_contents($url, false, $context);
            if($img_data) {
                file_put_contents("../uploads/" . $new_filename, $img_data);
                $product_image = $new_filename;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, name, description, price, stock, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$seller_id, $category_id, $name, $description, $price, $stock, $product_image]);
        
        $_SESSION['success'] = "Product added successfully!";
        header("Location: ../index.php?page=seller_products");
        exit;
    }
    elseif($action == 'edit') {
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];
        
        // Verify ownership
        $stmt = $pdo->prepare("SELECT product_image FROM products WHERE id=? AND seller_id=?");
        $stmt->execute([$product_id, $seller_id]);
        $prod = $stmt->fetch();
        
        if(!$prod && $_SESSION['role'] != 'admin') {
            $_SESSION['error'] = "Unauthorized.";
            header("Location: ../index.php?page=seller_products");
            exit;
        }

        $product_image = $prod['product_image'];

        // Image Upload
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['product_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array(strtolower($ext), $allowed)) {
                $new_filename = time() . '_' . basename($filename);
                move_uploaded_file($_FILES['product_image']['tmp_name'], "../uploads/" . $new_filename);
                $product_image = $new_filename;
            }
        } elseif(!empty($_POST['image_url'])) {
            $url = $_POST['image_url'];
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $new_filename = time() . '_remote.' . $ext;
            
            $options = ["http" => ["header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"]];
            $context = stream_context_create($options);
            $img_data = @file_get_contents($url, false, $context);
            if($img_data) {
                file_put_contents("../uploads/" . $new_filename, $img_data);
                $product_image = $new_filename;
            }
        }

        $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, product_image=? WHERE id=?");
        $stmt->execute([$category_id, $name, $description, $price, $stock, $product_image, $product_id]);
        
        $_SESSION['success'] = "Product updated successfully!";
        // Admin uses the same action script but returns to admin_products
        $redirect = $_SESSION['role'] == 'admin' ? 'admin_products' : 'seller_products';
        header("Location: ../index.php?page=$redirect");
        exit;
    }
    elseif($action == 'delete') {
        $product_id = $_POST['product_id'];
        // Cascade handles everything else
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=? AND (seller_id=? OR ?='admin')");
        $stmt->execute([$product_id, $seller_id, $_SESSION['role']]);
        
        $_SESSION['success'] = "Product deleted.";
        $redirect = $_SESSION['role'] == 'admin' ? 'admin_products' : 'seller_products';
        header("Location: ../index.php?page=$redirect");
        exit;
    }
}
?>
