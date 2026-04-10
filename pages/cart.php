<?php
$customer_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.product_image, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id = ?");
$stmt->execute([$customer_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Shopping Cart</h3>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="rounded-card p-4">
            <?php if(count($cart_items) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="uploads/<?php echo htmlspecialchars($item['product_image']); ?>" width="50" height="50" class="rounded me-3" style="object-fit:contain" onerror="this.src='https://via.placeholder.com/50'">
                                        <span class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <form method="POST" action="actions/cart_action.php" class="d-flex align-items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="form-control form-control-sm text-center" style="width: 70px;" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="fw-bold">₹<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <form method="POST" action="actions/cart_action.php">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger rounded-circle"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Your cart is empty.</h5>
                    <a href="?page=browse_products" class="btn btn-primary-gradient rounded-pill mt-3">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if(count($cart_items) > 0): ?>
    <div class="col-lg-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-3">Order Summary</h5>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold">₹<?php echo number_format($total, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                <span class="text-muted">Shipping</span>
                <span class="text-success fw-bold">Free</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <h5 class="fw-bold">Total</h5>
                <h5 class="fw-bold text-gradient">₹<?php echo number_format($total, 2); ?></h5>
            </div>
            <a href="?page=checkout" class="btn btn-primary-gradient w-100 rounded-pill py-2 fw-bold">Proceed to Checkout</a>
        </div>
    </div>
    <?php endif; ?>
</div>
