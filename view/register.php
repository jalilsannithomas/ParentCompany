<?php include('header.php'); ?>

<div class="auth-container">
    <h1 class="auth-title">Register</h1>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert error">
            <?php
            if ($_GET['error'] === 'exists') echo 'Email already exists';
            else if ($_GET['error'] === 'password') echo 'Passwords do not match';
            else echo 'Registration failed';
            ?>
        </div>
    <?php endif; ?>

    <form action="../actions/register_action.php" method="POST" class="auth-form">
        <div class="form-group">
            <input type="text" name="name" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="tel" name="phone" placeholder="+233xxxxxxxxx" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button type="submit" name="register" class="auth-btn">Register</button>
    </form>

    <div class="auth-links">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

<?php include('footer.php'); ?>