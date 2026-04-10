<?php
$customer_id = $_SESSION['user_id'];

// Get Stats
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE customer_id=$customer_id")->fetchColumn();
$cart_items = $pdo->query("SELECT SUM(quantity) FROM cart WHERE customer_id=$customer_id")->fetchColumn();
$reviews_given = $pdo->query("SELECT COUNT(*) FROM reviews WHERE customer_id=$customer_id")->fetchColumn();
$wishlist_items = 0; // if we want to add wishlist later, but for now 0

// Get latest products
$latest_products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4")->fetchAll();

// Get order history data for chart
$orders = $pdo->query("SELECT DATE(created_at) as date, SUM(total_amount) as total FROM orders WHERE customer_id=$customer_id GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7")->fetchAll();
$dates = array_column($orders, 'date');
$totals = array_column($orders, 'total');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold"><span class="text-gradient">Welcome back,</span> <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
    <a href="?page=browse_products" class="btn btn-primary-gradient rounded-pill px-4">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Orders</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_orders ?: 0; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-primary">
                <i class="fas fa-box-open fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Cart Items</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $cart_items ?: 0; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-success">
                <i class="fas fa-shopping-cart fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Wishlist (Soon)</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $wishlist_items; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-danger">
                <i class="fas fa-heart fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Reviews Given</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $reviews_given ?: 0; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-warning">
                <i class="fas fa-star fs-4"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Order Spending History</h5>
            <div class="chart-container">
                <canvas id="orderChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="rounded-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">New Arrivals</h5>
            </div>
            
            <?php foreach($latest_products as $prod): ?>
            <div class="d-flex align-items-center mb-3">
                <img src="uploads/<?php echo htmlspecialchars($prod['product_image']); ?>" class="rounded" width="60" height="60" style="object-fit:cover;" onerror="this.src='https://via.placeholder.com/60'">
                <div class="ms-3">
                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($prod['name']); ?></h6>
                    <small class="text-muted">₹<?php echo htmlspecialchars($prod['price']); ?></small><br>
                    <a href="?page=product_details&id=<?php echo $prod['id']; ?>" class="text-decoration-none small text-gradient fw-bold">View Product</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('orderChart').getContext('2d');
    const orderChart = new Chart(ctx, {
        type: 'bar', // Using bar chart as requested for history
        data: {
            labels: <?php echo json_encode(array_reverse($dates)); ?>,
            datasets: [{
                label: 'Spent Amount (₹)',
                data: <?php echo json_encode(array_reverse($totals)); ?>,
                backgroundColor: '#ffc107',
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
