<?php
require_once 'config/db.php';

echo "Starting massive product import...\n";

// Clear previous products and categories
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE products;");
$pdo->exec("TRUNCATE TABLE categories;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

// Insert new categories
$categories = [
    1 => 'Men',
    2 => 'Women',
    3 => 'Kids',
    4 => 'Home & Living',
    5 => 'Beauty',
    6 => 'Gen Z'
];

foreach ($categories as $id => $name) {
    $stmt = $pdo->prepare("INSERT INTO categories (id, name) VALUES (?, ?)");
    $stmt->execute([$id, $name]);
}

// Ensure a seller exists
$stmt = $pdo->query("SELECT id FROM users WHERE role='seller' LIMIT 1");
$sellerId = $stmt->fetchColumn();

if (!$sellerId) {
    $pdo->exec("INSERT INTO users (full_name, email, password, role) VALUES ('Auto Seller', 'autoseller@example.com', '123456', 'seller')");
    $sellerId = $pdo->lastInsertId();
}

$products = [
    // Men (1)
    ['category_id' => 1, 'name' => 'Premium Men\'s Suit', 'desc' => 'Three-piece tailored suit crafted from fine Italian wool. Perfect for business and formal events.', 'price' => 299.99, 'stock' => 15, 'keyword' => 'mens+suit'],
    ['category_id' => 1, 'name' => 'Classic Chronograph Watch', 'desc' => 'Elegant stainless steel watch with a leather strap and waterproof features.', 'price' => 129.50, 'stock' => 40, 'keyword' => 'mens+watch'],
    ['category_id' => 1, 'name' => 'Casual Oxford Shoes', 'desc' => 'Comfortable and stylish genuine leather oxford shoes.', 'price' => 74.99, 'stock' => 60, 'keyword' => 'mens+shoes'],

    // Women (2)
    ['category_id' => 2, 'name' => 'Floral Summer Dress', 'desc' => 'Lightweight and breathable chiffon summer dress with floral patterns.', 'price' => 59.90, 'stock' => 100, 'keyword' => 'summer+dress'],
    ['category_id' => 2, 'name' => 'Designer Leather Handbag', 'desc' => 'Luxurious, spacious tote bag with gold-plated accents.', 'price' => 199.00, 'stock' => 20, 'keyword' => 'handbag'],
    ['category_id' => 2, 'name' => 'Stiletto High Heels', 'desc' => 'Classic black velvet stiletto heels for the perfect evening out.', 'price' => 89.99, 'stock' => 35, 'keyword' => 'high+heels'],

    // Kids (3)
    ['category_id' => 3, 'name' => 'Colorful Building Blocks', 'desc' => '100-piece creative building blocks set that encourages imagination.', 'price' => 24.99, 'stock' => 200, 'keyword' => 'toys'],
    ['category_id' => 3, 'name' => 'Kids Denim Overalls', 'desc' => 'Durable and cute denim overalls for toddlers and young children.', 'price' => 34.50, 'stock' => 80, 'keyword' => 'kids+clothes'],
    ['category_id' => 3, 'name' => 'Educational Tablet', 'desc' => 'Interactive kids tablet packed with learning apps and parental controls.', 'price' => 99.99, 'stock' => 45, 'keyword' => 'kids+tablet'],

    // Home & Living (4)
    ['category_id' => 4, 'name' => 'Velvet Sofa Couch', 'desc' => 'Plush 3-seater velvet sofa in emerald green, bringing elegance to your living room.', 'price' => 549.00, 'stock' => 10, 'keyword' => 'sofa'],
    ['category_id' => 4, 'name' => 'Aromatherapy Diffuser', 'desc' => 'Ultrasonic essential oil diffuser with ambient LED lighting.', 'price' => 29.99, 'stock' => 150, 'keyword' => 'diffuser'],
    ['category_id' => 4, 'name' => 'Egyptian Cotton Sheets', 'desc' => 'Ultra-soft, 800-thread count king size bed sheet set.', 'price' => 119.50, 'stock' => 55, 'keyword' => 'bed+sheets'],

    // Beauty (5)
    ['category_id' => 5, 'name' => 'Hyaluronic Acid Serum', 'desc' => 'Deep hydrating face serum designed to reduce fine lines and wrinkles.', 'price' => 45.00, 'stock' => 120, 'keyword' => 'face+serum'],
    ['category_id' => 5, 'name' => 'Matte Liquid Lipstick Set', 'desc' => 'A set of 5 long-lasting, waterproof matte lipsticks in neutral shades.', 'price' => 35.99, 'stock' => 85, 'keyword' => 'lipstick'],
    ['category_id' => 5, 'name' => 'Professional Makeup Brushes', 'desc' => '12-piece synthetic makeup brush set with a premium leather travel case.', 'price' => 55.00, 'stock' => 60, 'keyword' => 'makeup+brush'],

    // Gen Z (6)
    ['category_id' => 6, 'name' => 'Oversized Graphic Tee', 'desc' => 'Trendy streetwear oversized t-shirt with retro aesthetic prints.', 'price' => 29.90, 'stock' => 200, 'keyword' => 'streetwear'],
    ['category_id' => 6, 'name' => 'LED Strip Room Lights', 'desc' => '65ft RGB smart LED light strips with Bluetooth app synchronization.', 'price' => 22.99, 'stock' => 300, 'keyword' => 'led+lights'],
    ['category_id' => 6, 'name' => 'Chunky Platform Sneakers', 'desc' => 'Vintage-inspired thick sole platform sneakers for everyday aesthetic looks.', 'price' => 79.99, 'stock' => 90, 'keyword' => 'platform+sneakers']
];

// Create uploads folder if missing
if (!is_dir('uploads')) {
    mkdir('uploads');
}

foreach ($products as $idx => $p) {
    // Download image
    $filename = 'prod_' . time() . '_' . $idx . '.jpg';
    $path = 'uploads/' . $filename;
    
    // Fallback images based on keywords from Picsum
    $imageUrl = "https://picsum.photos/seed/" . md5($p['name']) . "/600/800";
    
    echo "Downloading image for: " . $p['name'] . "\n";
    $imgData = @file_get_contents($imageUrl);
    
    if ($imgData !== false) {
        file_put_contents($path, $imgData);
        $finalImage = $filename;
    } else {
        $finalImage = 'default_product.png'; // Fallback
    }
    
    $stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, name, description, price, stock, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $sellerId, 
        $p['category_id'], 
        $p['name'], 
        $p['desc'], 
        $p['price'], 
        $p['stock'], 
        $finalImage
    ]);
}

echo "Successfully injected " . count($products) . " real products across all categorized sections!\n";
?>
