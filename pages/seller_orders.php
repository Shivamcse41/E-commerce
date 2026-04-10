<?php
$seller_id = $_SESSION['user_id'];

// Get all orders containing this seller's products
$stmt = $pdo->prepare("
    SELECT o.*, GROUP_CONCAT(p.name SEPARATOR ', ') as products_ordered 
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN products p ON oi.product_id = p.id 
    WHERE p.seller_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Order Management</h3>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="rounded-card p-4">
            <?php if(count($orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Products</th>
                                <th>Order Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                <td><span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($order['products_ordered']); ?>"><?php echo htmlspecialchars($order['products_ordered']); ?></span></td>
                                <td class="fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                        $badge_class = 'bg-secondary';
                                        if($order['status'] == 'Pending') $badge_class = 'bg-warning text-dark';
                                        if($order['status'] == 'Processing') $badge_class = 'bg-info text-dark';
                                        if($order['status'] == 'Shipped') $badge_class = 'bg-primary';
                                        if($order['status'] == 'Delivered') $badge_class = 'bg-success';
                                        if($order['status'] == 'Cancelled') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?> rounded-pill px-3 py-2"><?php echo htmlspecialchars($order['status']); ?></span>
                                </td>
                                <td>
                                    <form method="POST" action="actions/order_action.php" class="d-flex">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="form-select form-select-sm me-2" style="width:130px;">
                                            <option value="Pending" <?php echo $order['status']=='Pending'?'selected':''; ?>>Pending</option>
                                            <option value="Processing" <?php echo $order['status']=='Processing'?'selected':''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo $order['status']=='Shipped'?'selected':''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo $order['status']=='Delivered'?'selected':''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo $order['status']=='Cancelled'?'selected':''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-circle"><i class="fas fa-check"></i></button>
                                    </form>
                                    <!-- View Details Modal could be added similarly to customer_orders -->
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No orders for your products yet.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
