<?php
$stmt = $pdo->prepare("
    SELECT o.*, u.full_name as customer_name, u.email as customer_email
    FROM orders o 
    JOIN users u ON o.customer_id = u.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">All Orders (Global)</h3>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="rounded-card p-4">
            <?php if(count($orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Customer</th>
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
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                </td>
                                <td class="fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></td>
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
                                    <form method="POST" action="actions/order_action.php" class="d-flex align-items-center">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="form-select form-select-sm me-2" style="width:130px;">
                                            <option value="Pending" <?php echo $order['status']=='Pending'?'selected':''; ?>>Pending</option>
                                            <option value="Processing" <?php echo $order['status']=='Processing'?'selected':''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo $order['status']=='Shipped'?'selected':''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo $order['status']=='Delivered'?'selected':''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo $order['status']=='Cancelled'?'selected':''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-circle" title="Update Status"><i class="fas fa-check"></i></button>
                                        
                                        <!-- View Details Button -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle ms-1" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>

                                    <!-- Order Details Modal -->
                                    <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content border-0" style="border-radius:15px;">
                                                <div class="modal-header border-bottom-0">
                                                    <h5 class="modal-title fw-bold">Order #<?php echo htmlspecialchars($order['id']); ?> Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body pb-0">
                                                    <?php
                                                        $istmt = $pdo->prepare("SELECT oi.*, p.name, p.product_image, u.full_name as seller_name FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN users u ON p.seller_id=u.id WHERE oi.order_id = ?");
                                                        $istmt->execute([$order['id']]);
                                                        $items = $istmt->fetchAll();
                                                    ?>
                                                    <div class="table-responsive">
                                                        <table class="table align-middle">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Seller</th>
                                                                    <th>Price</th>
                                                                    <th>Qty</th>
                                                                    <th>Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($items as $itm): ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <img src="uploads/<?php echo htmlspecialchars($itm['product_image']); ?>" width="40" height="40" class="rounded me-2" style="object-fit:contain" onerror="this.src='https://via.placeholder.com/40'">
                                                                            <span><?php echo htmlspecialchars($itm['name']); ?></span>
                                                                        </div>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($itm['seller_name']); ?></td>
                                                                    <td>$<?php echo number_format($itm['price'], 2); ?></td>
                                                                    <td><?php echo $itm['quantity']; ?></td>
                                                                    <td class="fw-bold">$<?php echo number_format($itm['price'] * $itm['quantity'], 2); ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No orders found.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
