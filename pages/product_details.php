<?php
// pages/product_details.php
$id = isset($_GET['id']) ? $_GET['id'] : '';
if (!$id) {
    header("Location: index.php?page=landing");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
} catch (PDOException $e) {
    $p = null;
}

if (!$p) {
    echo "<div class='text-center py-5'><h3>Product not found.</h3></div>";
    return;
}
?>

<div class="details-container">
    <!-- Image Gallery -->
    <div class="image-gallery">
        <img src="uploads/<?php echo htmlspecialchars($p['product_image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='https://via.placeholder.com/600x800?text=No+Image'">
        <img src="uploads/<?php echo htmlspecialchars($p['product_image']); ?>" alt="Second view" onerror="this.src='https://via.placeholder.com/600x800?text=No+Image'">
        <img src="uploads/<?php echo htmlspecialchars($p['product_image']); ?>" alt="Third view" onerror="this.src='https://via.placeholder.com/600x800?text=No+Image'">
        <img src="uploads/<?php echo htmlspecialchars($p['product_image']); ?>" alt="Fourth view" onerror="this.src='https://via.placeholder.com/600x800?text=No+Image'">
    </div>

    <!-- Product Details Info -->
    <div class="product-info">
        <div style="font-weight:700; font-size: 24px; margin-bottom: 5px;">SmartCart Brand</div>
        <div class="subtitle"><?php echo htmlspecialchars($p['name']); ?></div>
        
        <div class="price-box">
            <span class="main-price">Rs. <?php echo number_format($p['price'], 0); ?></span>
            <span style="font-size: 18px; color: var(--muted); text-decoration: line-through;">Rs. <?php echo number_format($p['price'] * 1.2, 0); ?></span>
            <span style="font-size: 18px; color: #ff905a; font-weight: 700;">(20% OFF)</span>
        </div>
        
        <div style="font-weight: 700; color: #03a685; margin-bottom: 30px;">Inclusive of all taxes</div>

        <div style="font-weight: 700; margin-bottom: 10px;">SELECT SIZE <span style="color: var(--primary); font-size: 12px; margin-left: 15px; cursor: pointer;">SIZE CHART <i class="fas fa-chevron-right"></i></span></div>
        <div class="size-options d-flex gap-3 mb-4">
            <div style="width: 50px; height: 50px; border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer;">S</div>
            <div style="width: 50px; height: 50px; border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer;">M</div>
            <div style="width: 50px; height: 50px; border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer;">L</div>
            <div style="width: 50px; height: 50px; border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer;">XL</div>
        </div>

        <div class="d-flex gap-3 mb-5">
            <form method="POST" action="actions/cart_action.php" style="flex: 1;">
                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-bag"><i class="fas fa-shopping-bag me-2"></i> ADD TO BAG</button>
            </form>
            <form method="POST" action="actions/wishlist_action.php" style="flex: 1;">
                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                <button type="button" class="btn-wishlist"><i class="far fa-heart me-2"></i> WISHLIST</button>
            </form>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 20px;">
            <div style="font-weight: 700; margin-bottom: 15px;">PRODUCT DETAILS <i class="fas fa-file-alt ms-2"></i></div>
            <p style="color: #444; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
        </div>
    </div>
</div>
