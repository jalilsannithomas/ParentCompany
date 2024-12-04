<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Company ©</title>
<link rel="stylesheet" href="../css/theme.css?v=1.0">
<link rel="stylesheet" href="../css/override.css"> <!-- Added override stylesheet -->
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="../index.php" class="logo">Parent Company ©</a>
            <div class="nav-links">
                <a href="../view/shop.php">Shop</a>
                <a href="../view/raffle.php">Raffle</a>
                <a href="../view/troops.php">Troops</a>
                <a href="../view/user_dashboard.php" class="btn btn-secondary">Profile</a> <!-- Added Profile Button -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="cart.php" class="cart-link">Cart</a>
                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <a href="../admin/" class="admin-link">Admin Panel</a>
                    <?php endif; ?>
                    <a href="../actions/logout_action.php">Logout</a>
                <?php else: ?>
                    <a href="../view/login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <main>