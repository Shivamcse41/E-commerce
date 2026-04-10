<?php
// pages/wishlist.php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if (!$role || $role !== 'customer') {
    $_SESSION['error'] = "Please log in to view your wishlist!";
    header("Location: index.php?page=auth_form");
    exit;
}
?>

<div class="container my-5 text-center">
    <div style="max-width: 500px; margin: 0 auto; padding: 40px 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <i class="far fa-heart" style="font-size: 80px; color: var(--primary); margin-bottom: 20px;"></i>
        <h2 style="font-weight: 700; color: #333;">Your Wishlist</h2>
        <p class="text-muted mt-3 mb-4">
            We are currently building this feature! Very soon you'll be able to save your favorite items here for later.
        </p>
        <a href="index.php?page=landing" class="btn" style="background-color: var(--primary); color: white; padding: 10px 25px; border-radius: 5px; text-decoration: none; font-weight: 600;">
            Keep Shopping
        </a>
    </div>
</div>
