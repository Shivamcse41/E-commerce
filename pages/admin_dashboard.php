<?php
// Get Overall Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_sellers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Sales Analytics Chart (Overall Revenue)
$sales = $pdo->query("SELECT DATE(created_at) as date, SUM(total_amount) as total FROM orders GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7")->fetchAll();
$dates = array_column($sales, 'date');
$totals = array_column($sales, 'total');

// Product category distribution
$cat_dist = $pdo->query("SELECT c.name, COUNT(p.id) as count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id")->fetchAll();
$cat_names = array_column($cat_dist, 'name');
$cat_counts = array_column($cat_dist, 'count');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Admin Dashboard</h3>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Users</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_users; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-primary">
                <i class="fas fa-users fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Sellers</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_sellers; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-info">
                <i class="fas fa-user-tie fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Products</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_products; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-success">
                <i class="fas fa-box fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="rounded-card p-3 d-flex align-items-center justify-content-between smooth-anim product-card">
            <div>
                <h6 class="text-muted mb-1">Total Orders</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_orders; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-circle text-warning">
                <i class="fas fa-shopping-bag fs-4"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Overall Sales Analytics</h5>
            <div class="chart-container">
                <canvas id="adminSalesChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Category Distribution</h5>
            <div class="chart-container">
                <canvas id="catChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Chart
    const ctx1 = document.getElementById('adminSalesChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($dates)); ?>,
            datasets: [{
                label: 'Revenue (₹)',
                data: <?php echo json_encode(array_reverse($totals)); ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Category Distribution Chart
    const ctx2 = document.getElementById('catChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie', // Requested product category distribution
        data: {
            labels: <?php echo json_encode($cat_names); ?>,
            datasets: [{
                data: <?php echo json_encode($cat_counts); ?>,
                backgroundColor: ['#e94560', '#ff2e63', '#ffc107', '#17a2b8', '#28a745', '#6c757d']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>
