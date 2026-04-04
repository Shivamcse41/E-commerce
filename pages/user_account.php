<?php
// pages/user_account.php
// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=auth_form");
    exit();
}

$user_id = $_SESSION['user_id'];
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="account-container">
    <!-- Sidebar -->
    <aside class="account-sidebar">
        <h3 class="sidebar-title">Account</h3>
        <nav class="sidebar-menu">
            <a href="index.php?page=user_account&section=overview" class="sidebar-link <?php echo $section == 'overview' ? 'active' : ''; ?>">Overview</a>
            <a href="index.php?page=customer_orders" class="sidebar-link">Orders</a>
            <a href="index.php?page=wishlist" class="sidebar-link">Wishlist</a>
            <a href="index.php?page=user_account&section=profile" class="sidebar-link <?php echo $section == 'profile' ? 'active' : ''; ?>">Profile Details</a>
            <a href="actions/logout.php" class="sidebar-link" style="color:var(--primary); margin-top:20px;">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="account-content">
        <div class="content-card shadow-sm">
            <?php if ($section == 'overview'): ?>
                <h4 style="font-weight:700; margin-bottom:20px;">Overview</h4>
                <div style="display:flex; gap:30px; margin-bottom:40px;">
                    <div style="background:#fff3f6; padding:20px; flex:1; border-radius:4px; text-align:center;">
                        <div style="font-size:24px; font-weight:800; color:var(--primary);">12</div>
                        <div style="font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase;">Orders</div>
                    </div>
                    <div style="background:#f0faf7; padding:20px; flex:1; border-radius:4px; text-align:center;">
                        <div style="font-size:24px; font-weight:800; color:#03a685;">5</div>
                        <div style="font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase;">Wishlist Items</div>
                    </div>
                </div>
                
                <h5 style="font-weight:700; font-size:14px; margin-bottom:20px;">Personal Information</h5>
                <div style="font-size:14px; line-height:2.0;">
                    <div><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></div>
                    <div><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
                    <div><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender'] ?: 'Not Specified'); ?></div>
                </div>

            <?php elseif ($section == 'profile'): ?>
                <h4 style="font-weight:700; margin-bottom:30px;">Profile Details</h4>
                <form method="POST" action="actions/profile_action.php">
                    <div class="mb-4">
                        <label style="font-weight:700; font-size:12px; color:var(--muted); display:block; margin-bottom:5px;">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" style="max-width:400px; border:1px solid var(--border);">
                    </div>
                    <div class="mb-4">
                        <label style="font-weight:700; font-size:12px; color:var(--muted); display:block; margin-bottom:5px;">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="max-width:400px; background:#f5f5f5; border:1px solid var(--border);">
                    </div>
                    <div class="mb-4">
                        <label style="font-weight:700; font-size:12px; color:var(--muted); display:block; margin-bottom:5px;">Gender</label>
                        <select name="gender" class="form-select" style="max-width:400px; border:1px solid var(--border);">
                            <option value="Male" <?php echo $user['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $user['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $user['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label style="font-weight:700; font-size:12px; color:var(--muted); display:block; margin-bottom:5px;">Shipping Address</label>
                        <textarea name="address" class="form-control" style="max-width:400px; height:100px; border:1px solid var(--border);"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-dark px-5 font-weight-700" style="background:var(--dark); font-size:14px; padding:12px 30px;">SAVE CHANGES</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</div>
