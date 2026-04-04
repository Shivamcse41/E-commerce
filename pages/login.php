<?php
// actions/login_action.php logic inline or separate? The design says single login page. Let's do it inline for simplicity since it's just the login form POSTing to itself.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hardcode admin as requested for demo 'admin' / '123456'
    // but we also stored admin in DB. Let's just check DB.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        
        // Redirect to their respective dashboard
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<div class="login-bg">
    <div class="login-card smooth-anim text-center">
        <!-- Icon -->
        <div class="mb-4">
            <i class="fas fa-shopping-cart fa-3x text-gradient"></i>
            <h2 class="mt-2 fw-bold">SmartCart</h2>
            <p class="text-muted">Sign in to your account</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login">
            <div class="mb-3 text-start">
                <label class="form-label text-muted fw-semibold">Email / Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="text" name="email" class="form-control border-start-0 bg-light" required placeholder="admin or user@example.com">
                </div>
            </div>
            
            <div class="mb-4 text-start">
                <label class="form-label text-muted fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 bg-light" required placeholder="123456">
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary-gradient w-100 py-2 rounded-pill fw-bold">
                SIGN IN
            </button>
        </form>

        <div class="mt-4 text-muted small">
            <p class="mb-1">Demo Credentials:</p>
            Admin: admin / 123456 <br>
            Seller: seller@example.com / 123456 <br>
            Customer: customer@example.com / 123456
        </div>
    </div>
</div>
