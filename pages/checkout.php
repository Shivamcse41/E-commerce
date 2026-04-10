<?php
$customer_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.product_image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id = ?");
$stmt->execute([$customer_id]);
$cart_items = $stmt->fetchAll();

if(count($cart_items) == 0) {
    header("Location: index.php?page=cart");
    exit;
}

$total = 0;
foreach($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get user info for billing
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$customer_id]);
$user = $stmt->fetch();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Checkout</h3>
</div>

<form method="POST" action="actions/checkout_action.php">
<div class="row">
    <div class="col-lg-8">
        <div class="rounded-card p-4 mb-4">
            <h5 class="fw-bold mb-4">Billing & Shipping Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label">Shipping Address</label>
                    <textarea name="shipping_address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Payment Method</h5>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                <label class="form-check-label" for="cod">
                    Cash on Delivery (COD)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="card" value="Credit Card">
                <label class="form-check-label" for="card">
                    Credit / Debit Card (Demo Only - No details required)
                </label>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-4">Order Items</h5>
            <?php foreach($cart_items as $item): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <img src="uploads/<?php echo htmlspecialchars($item['product_image']); ?>" width="40" height="40" class="rounded me-2" style="object-fit:contain" onerror="this.src='https://via.placeholder.com/40'">
                    <div style="line-height:1.2;">
                        <small class="fw-bold d-block text-truncate" style="max-width:120px;"><?php echo htmlspecialchars($item['name']); ?></small>
                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                    </div>
                </div>
                <span class="text-muted">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
            <?php endforeach; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between mb-4 mt-3">
                <h5 class="fw-bold">Total</h5>
                <h5 class="fw-bold text-gradient">₹<?php echo number_format($total, 2); ?></h5>
            </div>
            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            <button type="submit" class="btn btn-primary-gradient w-100 rounded-pill py-2 fw-bold">Place Order</button>
        </div>
    </div>
</div>
</form>
