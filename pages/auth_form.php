<?php
// pages/auth_form.php
?>
<div class="auth-bg">
    <div class="auth-card" id="authCard">
        <div class="auth-header">
            <h2 id="authTitle">Login</h2>
            <p id="authSubtitle">Please enter your credentials to continue</p>
        </div>
        
        <div class="auth-body">
            <!-- Login Form -->
            <form id="loginForm" method="POST" action="actions/auth_action.php?action=login">
                <div class="auth-input-group">
                    <label>Email / Username</label>
                    <input type="text" name="email" required placeholder="user@example.com">
                </div>
                <div class="auth-input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <button type="submit" class="auth-btn">LOGIN</button>
                
                <div class="text-center mt-3 small">
                    New to SmartCart? <a href="javascript:void(0)" onclick="toggleAuth('signup')" style="color:var(--primary); font-weight:700;">Create Account</a>
                </div>
            </form>

            <!-- Signup Form (Hidden by default) -->
            <form id="signupForm" method="POST" action="actions/auth_action.php?action=signup" style="display:none;">
                <div class="auth-input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="Enter your name">
                </div>
                <div class="auth-input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter email">
                </div>
                <div class="auth-input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Choose a secure password">
                </div>
                <div class="auth-input-group">
                    <label>Gender (Optional)</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" class="auth-btn">CREATE ACCOUNT</button>
                
                <div class="text-center mt-3 small">
                    Already have an account? <a href="javascript:void(0)" onclick="toggleAuth('login')" style="color:var(--primary); font-weight:700;">Login Now</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAuth(mode) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const authTitle = document.getElementById('authTitle');
    const authSubtitle = document.getElementById('authSubtitle');

    if (mode === 'signup') {
        loginForm.style.display = 'none';
        signupForm.style.display = 'block';
        authTitle.innerText = 'Signup';
        authSubtitle.innerText = 'Join SmartCart for a better experience';
    } else {
        loginForm.style.display = 'block';
        signupForm.style.display = 'none';
        authTitle.innerText = 'Login';
        authSubtitle.innerText = 'Please enter your credentials to continue';
    }
}

// Support hash-based navigation for signup
window.addEventListener('load', () => {
    if (window.location.hash === '#signup') {
        toggleAuth('signup');
    }
});
</script>
