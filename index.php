<?php
session_start();
require_once 'config/db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Allow public access to landing, products, and setup
$public_pages = ['landing', 'browse_products', 'product_details', 'auth_form', 'setup'];
if (!$role && !in_array($page, $public_pages)) {
    $page = 'auth_form';
}

require_once 'includes/header.php';

// Special full-width layout for customers and guests
$is_ecommerce_view = (!$role || $role == 'customer');

if ($is_ecommerce_view) {
    // If a customer hits 'home', they see the landing page.
    if ($page == 'home') $page = 'landing';
    
    // Whitelist for e-commerce pages
    $allowed_ecommerce = ['landing', 'browse_products', 'product_details', 'cart', 'checkout', 'customer_orders', 'notifications', 'feedback', 'wishlist', 'auth_form', 'user_account'];
    
    if (!in_array($page, $allowed_ecommerce)) {
        $page = 'landing';
    }

    require_once 'includes/landing_navbar.php'; // This will be our main Myntra Navbar
    
    // Alerts show up here too
    if(isset($_SESSION['success'])) {
        echo '<div class="alert alert-success m-3">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger m-3">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }

    require_once "pages/{$page}.php";
} elseif ($page == 'login') {
    require_once 'pages/login.php';
} else {
    // Dashboard layout for Admins and Sellers
    echo '<div class="wrapper">';
    require_once 'includes/sidebar.php';
    
    echo '<div id="content">';
    require_once 'includes/navbar.php';
    
    echo '<div class="container-fluid p-4">';
    
    // Alert messaging
    if(isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }

    // Role Based Routing for Dashboard
    $allowed_pages = [];
    if($role == 'admin') {
        $allowed_pages = ['admin_dashboard', 'admin_categories', 'admin_products', 'admin_users', 'admin_orders', 'notifications'];
    } elseif($role == 'seller') {
        $allowed_pages = ['seller_dashboard', 'seller_products', 'seller_orders', 'notifications'];
    }

    if($page == 'home') {
        if($role == 'admin') $page = 'admin_dashboard';
        if($role == 'seller') $page = 'seller_dashboard';
    }

    if(in_array($page, $allowed_pages)) {
        require_once "pages/{$page}.php";
    } else {
        echo "<h3>Page not found or access denied.</h3>";
    }
    
    echo '</div>'; // End container
    echo '</div>'; // End content
    echo '</div>'; // End wrapper
}

require_once 'includes/footer.php';
?>
