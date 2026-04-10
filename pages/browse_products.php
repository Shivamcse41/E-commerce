<?php
// pages/browse_products.php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_name = isset($_GET['category']) ? $_GET['category'] : '';

// Base query for products including categories
$query = "SELECT p.*, c.name as category_name, 
          COALESCE((SELECT AVG(rating) FROM reviews WHERE product_id=p.id), 0) as avg_rating 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.stock > 0";
$params = [];

if($search) {
    $query .= " AND (p.name LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if($category_name) {
    if ($category_name == 'Genz') {
        // Genz might be a specific tag or just a category. 
        // For now, let's assume it's a category.
        $query .= " AND c.name = ?";
        $params[] = $category_name;
    } else {
        $query .= " AND c.name = ?";
        $params[] = $category_name;
    }
}

$query .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all categories for filters
$all_categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="listing-container">
    <!-- Filters Sidebar -->
    <aside class="filters-sidebar">
        <div class="filter-section">
            <h4 class="filter-title">Categories</h4>
            <?php foreach ($all_categories as $cat): ?>
                <label class="filter-item">
                    <input type="checkbox" <?php echo ($category_name == $cat['name']) ? 'checked' : ''; ?> onclick="window.location.href='index.php?page=browse_products&category=<?php echo urlencode($cat['name']); ?>'">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="filter-section">
            <h4 class="filter-title">Price Range</h4>
            <label class="filter-item"><input type="checkbox"> ₹199 to ₹599</label>
            <label class="filter-item"><input type="checkbox"> ₹600 to ₹999</label>
            <label class="filter-item"><input type="checkbox"> ₹1000+</label>
        </div>

        <div class="filter-section">
            <h4 class="filter-title">Customer Rating</h4>
            <label class="filter-item"><input type="checkbox"> 4 Stars & Up</label>
            <label class="filter-item"><input type="checkbox"> 3 Stars & Up</label>
        </div>
    </aside>

    <!-- Main Product Grid -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0" style="font-size: 18px; letter-spacing: 1px;">
                <?php echo $category_name ? strtoupper(htmlspecialchars($category_name)) : 'ALL PRODUCTS'; ?>
                <span style="font-weight: 300; font-size: 14px; text-transform: lowercase; color: var(--muted);"> - <?php echo count($products); ?> items</span>
            </h2>
            
            <div class="sort-box">
                <select class="form-select form-select-sm border-0 bg-transparent fw-bold" style="cursor: pointer;">
                    <option>Sort by: Recommended</option>
                    <option>Sort by: Newest</option>
                    <option>Sort by: Price (High to Low)</option>
                    <option>Sort by: Price (Low to High)</option>
                </select>
            </div>
        </div>

        <div class="product-grid">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $prod): ?>
                    <a href="index.php?page=product_details&id=<?php echo $prod['id']; ?>" class="product-card">
                        <div class="img-wrapper">
                            <img src="uploads/<?php echo htmlspecialchars($prod['product_image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
                        </div>
                        <div class="card-details">
                            <div class="brand">SmartCart Brand</div>
                            <div class="name"><?php echo htmlspecialchars($prod['name']); ?></div>
                            <div class="price">
                                ₹<?php echo number_format($prod['price'], 0); ?>
                                <span class="original-price">₹<?php echo number_format($prod['price'] * 1.2, 0); ?></span>
                                <span class="discount">(20% OFF)</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 w-100">
                    <h4 class="text-muted">No products found in this section.</h4>
                    <p>Try searching for something else or browse another category.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
