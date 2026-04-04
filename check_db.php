<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT id, name, product_image, stock, seller_id, category_id FROM products ORDER BY id DESC LIMIT 10");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID:{$row['id']} | Name:{$row['name']} | Img:{$row['product_image']} | Stock:{$row['stock']} | Seller:{$row['seller_id']} | Category:{$row['category_id']}\n";
}
?>
