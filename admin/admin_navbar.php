<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="admin-navbar">
    <div class="nav-links">
        <a href="index.php" class="nav-item">DASHBOARD</a>
        <a href="products.php" class="nav-item">PRODUCTS</a>
        <a href="orders.php" class="nav-item">ORDERS</a>
        <a href="raffle.php" class="nav-item">RAFFLE ENTRIES</a>
        <a href="customers.php" class="nav-item">CUSTOMERS</a>
        <a href="../index.php" class="nav-item">VIEW STORE</a>
        <a href="../actions/logout.php" class="nav-item">LOGOUT</a>
    </div>
</nav>

<style>
.admin-navbar {
    background: #2b2b2b;
    padding: 0.8rem 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    border-bottom: 1px solid #3d3d3d;
}

.nav-links {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.nav-item {
    color: #ffffff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: color 0.3s ease;
}

.nav-item:hover {
    color: #4a90e2;
}

.nav-item.active {
    color: #4a90e2;
}
</style>
