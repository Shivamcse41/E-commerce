<nav id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-shopping-cart text-gradient"></i> SmartCart</h3>
    </div>
    
    <div class="text-center p-3">
        <?php $pic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default.png'; ?>
        <img src="uploads/<?php echo htmlspecialchars($pic); ?>" alt="Profile" class="profile-pic mb-2" onerror="this.src='https://via.placeholder.com/40'">
        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <small class="text-muted text-uppercase"><?php echo htmlspecialchars($_SESSION['role']); ?></small>
    </div>

    <ul class="list-unstyled components mt-3">
        <?php if($role == 'admin'): ?>
            <li class="<?php echo $page=='admin_dashboard'?'active':''; ?>">
                <a href="?page=admin_dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo $page=='admin_categories'?'active':''; ?>">
                <a href="?page=admin_categories"><i class="fas fa-list"></i> Categories</a>
            </li>
            <li class="<?php echo $page=='admin_products'?'active':''; ?>">
                <a href="?page=admin_products"><i class="fas fa-box"></i> Products</a>
            </li>
            <li class="<?php echo $page=='admin_users'?'active':''; ?>">
                <a href="?page=admin_users"><i class="fas fa-users"></i> Users</a>
            </li>
            <li class="<?php echo $page=='admin_orders'?'active':''; ?>">
                <a href="?page=admin_orders"><i class="fas fa-shopping-bag"></i> Orders</a>
            </li>
            <li class="<?php echo $page=='notifications'?'active':''; ?>">
                <a href="?page=notifications"><i class="fas fa-bell"></i> Notifications</a>
            </li>

        <?php elseif($role == 'seller'): ?>
            <li class="<?php echo $page=='seller_dashboard'?'active':''; ?>">
                <a href="?page=seller_dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo $page=='seller_products'?'active':''; ?>">
                <a href="?page=seller_products"><i class="fas fa-box"></i> My Products</a>
            </li>
            <li class="<?php echo $page=='seller_orders'?'active':''; ?>">
                <a href="?page=seller_orders"><i class="fas fa-shopping-bag"></i> Orders</a>
            </li>
            <li class="<?php echo $page=='notifications'?'active':''; ?>">
                <a href="?page=notifications"><i class="fas fa-bell"></i> Notifications</a>
            </li>

        <?php elseif($role == 'customer'): ?>
            <li class="<?php echo $page=='customer_home'?'active':''; ?>">
                <a href="?page=customer_home"><i class="fas fa-home"></i> Home</a>
            </li>
            <li class="<?php echo $page=='browse_products'?'active':''; ?>">
                <a href="?page=browse_products"><i class="fas fa-store"></i> Browse Products</a>
            </li>
            <li class="<?php echo $page=='cart'?'active':''; ?>">
                <a href="?page=cart"><i class="fas fa-shopping-cart"></i> Cart</a>
            </li>
            <li class="<?php echo $page=='customer_orders'?'active':''; ?>">
                <a href="?page=customer_orders"><i class="fas fa-box-open"></i> My Orders</a>
            </li>
            <li class="<?php echo $page=='notifications'?'active':''; ?>">
                <a href="?page=notifications"><i class="fas fa-bell"></i> Notifications</a>
            </li>
            <li class="<?php echo $page=='feedback'?'active':''; ?>">
                <a href="?page=feedback"><i class="fas fa-comment-dots"></i> Feedback</a>
            </li>
        <?php endif; ?>
        
        <li>
            <a href="actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</nav>
