<?php
$customer_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">My Orders</h3>
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
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
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
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill mb-1" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>

                            <!-- Order Details Modal -->
                            <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0" style="border-radius:15px;">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold">Order #<?php echo htmlspecialchars($order['id']); ?> Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body pb-0">
                                            <?php
                                                $istmt = $pdo->prepare("SELECT oi.*, p.name, p.product_image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                                $istmt->execute([$order['id']]);
                                                $items = $istmt->fetchAll();
                                            ?>
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Price</th>
                                                            <th>Qty</th>
                                                            <th>Subtotal</th>
                                                            <?php if($order['status'] == 'Delivered'): ?>
                                                            <th>Review</th>
                                                            <?php endif; ?>
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
                                                            <td>₹<?php echo number_format($itm['price'], 2); ?></td>
                                                            <td><?php echo $itm['quantity']; ?></td>
                                                            <td class="fw-bold">₹<?php echo number_format($itm['price'] * $itm['quantity'], 2); ?></td>
                                                            <?php if($order['status'] == 'Delivered'): ?>
                                                            <td><a href="?page=product_details&id=<?php echo $itm['product_id']; ?>#reviews" class="btn btn-sm btn-warning"><i class="fas fa-star"></i></a></td>
                                                            <?php endif; ?>
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
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">You have not placed any orders yet.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
