<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Parent Company</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">
            <span>Dashboard</span>
        </a>
        <a href="products.php" class="<?= $current_page === 'products.php' ? 'active' : '' ?>">
            <span>Products</span>
        </a>
        <a href="orders.php" class="<?= $current_page === 'orders.php' ? 'active' : '' ?>">
            <span>Orders</span>
        </a>
        <a href="raffle.php" class="<?= $current_page === 'raffle.php' ? 'active' : '' ?>">
            <span>Raffle Entries</span>
        </a>
        <a href="customers.php" class="<?= $current_page === 'customers.php' ? 'active' : '' ?>">
            <span>Customers</span>
        </a>
        <a href="../index.php"><span>View Store</span></a>
        <a href="../actions/logout_action.php"><span>Logout</span></a>
    </div>
    </nav>


</div>