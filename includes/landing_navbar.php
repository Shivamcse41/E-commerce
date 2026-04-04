<?php
// landing_navbar.php (Acting as the main Myntra Navbar)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

// Categories for the navbar
$nav_categories = [
    'Men' => 'Men',
    'Women' => 'Women',
    'Kids' => 'Kids',
    'Home & Living' => 'Home & Living',
    'Beauty' => 'Beauty',
    'Genz' => 'Genz'
];
?>

<link rel="stylesheet" href="assets/myntra.css">

<nav class="myntra-nav">
    <a href="index.php?page=landing" class="logo">Smart<span>Cart</span></a>
    
    <div class="nav-menu">
        <?php foreach ($nav_categories as $key => $val): ?>
            <a href="index.php?page=browse_products&category=<?php echo urlencode($val); ?>"><?php echo htmlspecialchars($key); ?></a>
        <?php endforeach; ?>
        <a href="#" style="position:relative;">Studio <span class="badge bg-danger rounded-pill" style="font-size: 8px; position: absolute; top: 18px; right: -25px;">NEW</span></a>
    </div>

    <form class="nav-search" action="index.php" method="GET">
        <input type="hidden" name="page" value="browse_products">
        <i class="fas fa-search"></i>
        <input type="text" name="search" placeholder="Search for products, brands and more" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    </form>

    <div class="nav-actions">
        <!-- Profile -->
        <div class="action-item">
            <a href="index.php?page=<?php echo $role ? 'user_account' : 'auth_form'; ?>" class="text-decoration-none text-dark d-flex flex-column align-items-center">
                <i class="far fa-user"></i>
                <span>Profile</span>
            </a>
            
            <div class="profile-dropdown">
                <?php if ($role): ?>
                    <div style="font-weight:700; font-size:14px; margin-bottom:5px;">Hello <?php echo htmlspecialchars($user_name); ?></div>
                    <div style="font-size:12px; color:var(--muted); margin-bottom:15px;">Welcome to your premium store</div>
                <?php else: ?>
                    <div style="font-weight:700; font-size:14px; margin-bottom:5px;">Welcome</div>
                    <div style="font-size:12px; color:var(--muted); margin-bottom:15px;">To access account and manage orders</div>
                    <a href="index.php?page=auth_form" style="display:block; border:1px solid var(--border); padding:10px; text-align:center; color:var(--primary); font-weight:700; text-decoration:none; margin-bottom:15px;">LOGIN / SIGNUP</a>
                <?php endif; ?>

                <ul style="list-style:none; padding:0; margin:0; border-top:1px solid var(--border); padding-top:10px;">
                    <li><a href="index.php?page=user_account&section=overview" style="display:block; padding:5px 0; text-decoration:none; color:var(--dark); font-size:14px;">Orders</a></li>
                    <li><a href="index.php?page=user_account&section=profile" style="display:block; padding:5px 0; text-decoration:none; color:var(--dark); font-size:14px;">My Profile</a></li>
                    <li><a href="index.php?page=wishlist" style="display:block; padding:5px 0; text-decoration:none; color:var(--dark); font-size:14px;">Wishlist</a></li>
                    <li><a href="index.php?page=feedback" style="display:block; padding:5px 0; text-decoration:none; color:var(--dark); font-size:14px;">Contact Us</a></li>
                </ul>
                
                <div style="border-top:1px solid var(--border); margin:10px 0;"></div>
                
                <ul style="list-style:none; padding:0; margin:0;">
                    <li><a href="#" style="display:block; padding:5px 0; text-decoration:none; color:var(--dark); font-size:14px;">Coupons</a></li>
                    <li><a href="#" style="display:block; padding:5px 0; text-decoration:none; color:var(--dark); font-size:14px;">Saved Addresses</a></li>
                    <?php if ($role): ?>
                        <div style="border-top:1px solid var(--border); margin:10px 0;"></div>
                        <li><a href="actions/logout.php" style="display:block; padding:5px 0; text-decoration:none; color:var(--primary); font-weight:700; font-size:14px;">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Wishlist -->
        <a href="index.php?page=wishlist" class="action-item">
            <i class="far fa-heart"></i>
            <span>Wishlist</span>
        </a>

        <!-- Cart -->
        <a href="index.php?page=cart" class="action-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Bag</span>
        </a>
    </div>
</nav>
