<?php
require_once 'config/db.php';

// Find the Gen Z category ID
$stmt = $pdo->prepare("SELECT id FROM categories WHERE name='Gen Z' LIMIT 1");
$stmt->execute();
$genz_cat_id = $stmt->fetchColumn();

if (!$genz_cat_id) {
    echo "Category 'Gen Z' not found!\n";
    exit;
}

// Find a seller 
$stmt = $pdo->query("SELECT id FROM users WHERE role='seller' LIMIT 1");
$sellerId = $stmt->fetchColumn();

// 5 Trending curated Gen Z Products for 2024
$products = [
    ['category_id' => $genz_cat_id, 'name' => 'Stanley Quencher H2.0 FlowState Tumbler', 'desc' => 'The viral 40oz stainless steel vacuum insulated tumbler. Keeps water ice-cold for 11 hours with an ergonomic handle.', 'price' => 45.00, 'stock' => 50, 'keyword' => 'stanley+tumbler'],
    ['category_id' => $genz_cat_id, 'name' => 'UGG Tasman Slippers', 'desc' => 'Trending slip-on suede slippers with embroidered braid detail and plush wool lining. Perfect for indoor and outdoor wear.', 'price' => 110.00, 'stock' => 25, 'keyword' => 'UGG+slippers'],
    ['category_id' => $genz_cat_id, 'name' => 'Sunset Projection Lamp', 'desc' => 'Romantic golden hour halo sunset lamp. Ideal for bedroom aesthetic, photography, and TikTok videos.', 'price' => 24.99, 'stock' => 120, 'keyword' => 'sunset+lamp'],
    ['category_id' => $genz_cat_id, 'name' => 'Starface Hydro-Stars Pimple Patches', 'desc' => 'Cute star-shaped hydrocolloid pimple patches that absorb fluid and reduce inflammation overnight.', 'price' => 14.99, 'stock' => 300, 'keyword' => 'starface+patches'],
    ['category_id' => $genz_cat_id, 'name' => 'Oversized Vintage Wash Graphic Tee', 'desc' => 'Washed-out black oversized t-shirt with heavy metal inspired graphic. Top trending streetwear staple.', 'price' => 35.00, 'stock' => 80, 'keyword' => 'vintage+tee']
];

foreach ($products as $idx => $p) {
    // Download image
    $filename = 'genz_trend_' . time() . '_' . $idx . '.jpg';
    $path = 'uploads/' . $filename;
    
    // Picsum seed based on keyword
    $imageUrl = "https://picsum.photos/seed/" . md5($p['keyword']) . "/600/800";
    
    echo "Downloading trend image for: " . $p['name'] . "\n";
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

echo "Successfully injected 5 trending Gen Z products!\n";
?>
