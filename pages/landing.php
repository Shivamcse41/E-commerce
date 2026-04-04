<?php
// pages/landing.php
// Fetch all products to show in a Myntra-style grid
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 20");
    $all_products = $stmt->fetchAll();
} catch (PDOException $e) {
    $all_products = [];
}
?>

<div class="product-section p-4">
    <h2 class="section-title mb-4" style="font-size: 24px; font-weight: 700; letter-spacing: 2px;">NEW ARRIVALS</h2>
    
    <div class="product-grid">
        <?php if(!empty($all_products)): ?>
            <?php foreach($all_products as $p): ?>
                <a href="index.php?page=product_details&id=<?php echo $p['id']; ?>" class="product-card">
                    <div class="img-wrapper">
                        <img src="uploads/<?php echo htmlspecialchars($p['product_image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
                    </div>
                    <div class="card-details">
                        <div class="brand">SmartCart Brand</div>
                        <div class="name"><?php echo htmlspecialchars($p['name']); ?></div>
                        <div class="price">
                            Rs. <?php echo number_format($p['price'], 0); ?>
                            <span class="original-price">Rs. <?php echo number_format($p['price'] * 1.2, 0); ?></span>
                            <span class="discount">(20% OFF)</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found. Please add some products to the store.</p>
        <?php endif; ?>
    </div>
</div>
