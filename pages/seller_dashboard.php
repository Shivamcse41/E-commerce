<?php
$seller_id = $_SESSION['user_id'];

// Get Stats
$total_products = $pdo->query("SELECT COUNT(*) FROM products WHERE seller_id=$seller_id")->fetchColumn();

// Total Orders relates to order_items that have this seller's products
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT oi.order_id) FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.seller_id = ?");
$stmt->execute([$seller_id]);
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(oi.quantity * oi.price) FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.seller_id = ?");
$stmt->execute([$seller_id]);
$total_revenue = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT oi.order_id) FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE p.seller_id = ? AND o.status = 'Pending'");
$stmt->execute([$seller_id]);
$pending_orders = $stmt->fetchColumn();

// Get order history data for chart
$stmt = $pdo->prepare("SELECT DATE(o.created_at) as date, SUM(oi.quantity * oi.price) as total FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE p.seller_id = ? GROUP BY DATE(o.created_at) ORDER BY date DESC LIMIT 7");
$stmt->execute([$seller_id]);
$revenue_history = $stmt->fetchAll();
$dates = array_column($revenue_history, 'date');
$totals = array_column($revenue_history, 'total');

// Product performance chart (Top 5 selling products)
$stmt = $pdo->prepare("SELECT p.name, SUM(oi.quantity) as sold_qty FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.seller_id = ? GROUP BY p.id ORDER BY sold_qty DESC LIMIT 5");
$stmt->execute([$seller_id]);
$product_perf = $stmt->fetchAll();
$prod_names = array_column($product_perf, 'name');
$prod_qtys = array_column($product_perf, 'sold_qty');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Seller Dashboard</h3>
    <a href="?page=seller_products" class="btn btn-primary-gradient rounded-pill px-4"><i class="fas fa-plus me-2"></i> Add Product</a>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Products</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_products ?: 0; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-primary">
                <i class="fas fa-box fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Orders</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_orders ?: 0; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-success">
                <i class="fas fa-shopping-bag fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Revenue</h6>
                <h3 class="fw-bold mb-0 text-dark">₹<?php echo number_format($total_revenue, 2) ?: '0.00'; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-info">
                <i class="fas fa-wallet fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Pending Orders</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $pending_orders ?: 0; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-warning">
                <i class="fas fa-clock fs-4"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Revenue Analytics</h5>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Product Performance (Top 5)</h5>
            <div class="chart-container">
                <canvas id="perfChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Chart
    const ctx1 = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($dates)); ?>,
            datasets: [{
                label: 'Revenue (₹)',
                data: <?php echo json_encode(array_reverse($totals)); ?>,
                borderColor: '#e94560',
                backgroundColor: 'rgba(233, 69, 96, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Product Performance Chart
    const ctx2 = document.getElementById('perfChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($prod_names); ?>,
            datasets: [{
                data: <?php echo json_encode($prod_qtys); ?>,
                backgroundColor: ['#e94560', '#ff2e63', '#ffc107', '#17a2b8', '#28a745']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>
