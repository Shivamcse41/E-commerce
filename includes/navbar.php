<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <button type="button" id="sidebarToggle" class="navbar-btn">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            
            <?php 
                // Notification badge count
                $notif_count = 0;
                if(isset($pdo) && isset($_SESSION['user_id'])) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=FALSE");
                    $stmt->execute([$_SESSION['user_id']]);
                    $notif_count = $stmt->fetchColumn();
                }
            ?>
            <a href="?page=notifications" class="text-dark position-relative me-4">
                <i class="fas fa-bell fs-5"></i>
                <?php if($notif_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                    <?php echo $notif_count; ?>
                </span>
                <?php endif; ?>
            </a>
            
            <?php if($role == 'customer'): ?>
            <?php 
                // Cart badge count
                $cart_count = 0;
                if(isset($pdo) && isset($_SESSION['user_id'])) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE customer_id=?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $cart_count = $stmt->fetchColumn();
                }
            ?>
            <a href="?page=cart" class="text-dark position-relative me-4">
                <i class="fas fa-shopping-cart fs-5"></i>
                <?php if($cart_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.6rem;">
                    <?php echo $cart_count; ?>
                </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_pic'] ?? 'default.png'); ?>" alt="Profile" class="profile-pic me-2" onerror="this.src='https://via.placeholder.com/40'">
                    <span class="fw-medium"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <!-- Profile logic can be added later -->
                    <li><a class="dropdown-item" href="actions/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
