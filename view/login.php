<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('header.php'); 
?>

<div class="auth-container">
    <h1 class="auth-title">Login</h1>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert error">
            <?= $_GET['error'] === 'invalid' ? 'Invalid credentials' : 'Login failed' ?>
        </div>
    <?php endif; ?>

    <form action="../actions/login_action.php" method="POST" class="auth-form">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="auth-btn">Login</button>
    </form>

    <div class="auth-links">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>

<style>
.auth-container {
    max-width: 400px;
    margin: 4rem auto;
    padding: 2rem;
    background: #111;
    border-radius: 8px;
}

.auth-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2rem;
    letter-spacing: 1px;
}

.auth-form .form-group {
    margin-bottom: 1.5rem;
}

.auth-form input {
    width: 100%;
    padding: 1rem;
    background: #222;
    border: 1px solid #333;
    color: #fff;
    border-radius: 4px;
}

.auth-btn {
    width: 100%;
    padding: 1rem;
    background: #fff;
    color: #000;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    font-size: 1.1rem;
    cursor: pointer;
    margin-bottom: 1rem;
}

.auth-links {
    text-align: center;
    color: #888;
}

.auth-links a {
    color: #fff;
    text-decoration: none;
    border-bottom: 1px solid #fff;
}
</style>

<?php include('footer.php'); ?>