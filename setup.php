<?php
require_once 'config/db.php';

try {
    // Drop and Recreate database for demo if creating fresh
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ecommerce_db");
    $pdo->exec("USE ecommerce_db");

    // 1 users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'seller', 'customer') NOT NULL,
        gender VARCHAR(20),
        address TEXT,
        profile_pic VARCHAR(255) DEFAULT 'default.png',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2 categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3 products
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        seller_id INT NOT NULL,
        category_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        product_image VARCHAR(255) DEFAULT 'default_product.png',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )");

    // 4 cart
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // 5 orders
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 6 order_items
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // 7 payments
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
        transaction_id VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )");

    // 8 reviews
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        customer_id INT NOT NULL,
        rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
        review TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 9 notifications
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        message TEXT NOT NULL,
        type ENUM('customer', 'seller', 'admin', 'general') DEFAULT 'general',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 10 feedback
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "<h1>Database and tables created successfully.</h1>";

    // Insert sample data
    // Admin
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email='admin'");
    $stmt->execute();
    if($stmt->fetchColumn() == 0) {
        // As per requirements: Admin login email = admin, password = 123456
        $pdo->exec("INSERT INTO users (full_name, email, password, role) VALUES ('Admin', 'admin', '123456', 'admin')");
    }

    // Seller
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email='seller@example.com'");
    $stmt->execute();
    if($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO users (full_name, email, password, role) VALUES ('Sample Seller', 'seller@example.com', '123456', 'seller')");
    }

    // Customer
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email='customer@example.com'");
    $stmt->execute();
    if($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO users (full_name, email, password, role) VALUES ('Sample Customer', 'customer@example.com', '123456', 'customer')");
    }
    
    // Check if we need to insert sample products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    if($stmt->fetchColumn() == 0) {
        $sellerId = $pdo->query("SELECT id FROM users WHERE role='seller' LIMIT 1")->fetchColumn();
        
        $pdo->exec("INSERT INTO categories (name) VALUES ('Electronics'), ('Clothing'), ('Books')");
        
        $pdo->exec("INSERT INTO products (seller_id, category_id, name, description, price, stock) VALUES 
            ($sellerId, 1, 'Smartphone X', 'High-end smartphone with OLED display', 799.99, 50),
            ($sellerId, 1, 'Laptop Pro', 'Ultra-fast laptop for professionals', 1299.99, 30),
            ($sellerId, 2, 'Cotton T-Shirt', 'Comfortable 100% cotton t-shirt', 19.99, 100),
            ($sellerId, 2, 'Denim Jeans', 'Classic blue denim jeans', 49.99, 80),
            ($sellerId, 3, 'The Great Gatsby', 'Classic novel by F. Scott Fitzgerald', 9.99, 150)
        ");
        echo "<p>Sample data inserted successfully.</p>";
    }
    
    echo "<p><a href='index.php'>Go to Home</a></p>";

} catch(PDOException $e) {
    echo "Database query error: " . $e->getMessage();
}
?>
