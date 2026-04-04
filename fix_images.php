<?php
$host = 'localhost';
$dbname = 'ecommerce_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("UPDATE products SET product_image='smartphone_x.png' WHERE name='Smartphone X'");
    $pdo->exec("UPDATE products SET product_image='laptop_pro.png' WHERE name='Laptop Pro'");
    $pdo->exec("UPDATE products SET product_image='tshirt.png' WHERE name='Cotton T-Shirt'");
    $pdo->exec("UPDATE products SET product_image='jeans.png' WHERE name='Denim Jeans'");
    $pdo->exec("UPDATE products SET product_image='the_great_gatsby.png' WHERE name='The Great Gatsby'");
    
    echo "Database updated successfully.\n";
    
    $stmt = $pdo->query("SELECT name, product_image FROM products");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['name'] . ' -> ' . $row['product_image'] . "\n";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
