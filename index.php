<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('includes/header.php');
include("controllers/product_controller.php");

// Initialize error message variable
$error_msg = '';

try {
    $products = get_all_products_ctr();
    error_log("Products retrieved: " . count($products));
} catch (Exception $e) {
    error_log("Error retrieving products: " . $e->getMessage());
    $products = [];
}

$size = isset($_GET['size']) ? $_GET['size'] : '';
$color = isset($_GET['color']) ? $_GET['color'] : '';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';

if($size || $color || $price_range) {
    try {
        $products = filter_products_ctr($size, $color, $price_range);
        error_log("Filtered products: " . count($products));
    } catch (Exception $e) {
        error_log("Error filtering products: " . $e->getMessage());
    }
}
?>

<div id="store-page" class="section active">
    <h1 class="page-title">Welcome to Our Store</h1>
    
    <div class="filter-section">
        <form action="" method="GET" class="filter-form">
            <div class="filter-group">
                <label>Size:</label>
                <select name="size">
                    <option value="">All Sizes</option>
                    <option value="Medium" <?= $size === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="Large" <?= $size === 'Large' ? 'selected' : '' ?>>Large</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Color:</label>
                <select name="color">
                    <option value="">All Colors</option>
                    <option value="White" <?= $color === 'White' ? 'selected' : '' ?>>White</option>
                    <option value="Black" <?= $color === 'Black' ? 'selected' : '' ?>>Black</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Price Range:</label>
                <select name="price">
                    <option value="">All Prices</option>
                    <option value="0-25" <?= $price_range === '0-25' ? 'selected' : '' ?>>Under $25</option>
                    <option value="25-50" <?= $price_range === '25-50' ? 'selected' : '' ?>>$25 - $50</option>
                    <option value="50+" <?= $price_range === '50+' ? 'selected' : '' ?>>$50+</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <?php if($size || $color || $price_range): ?>
                <a href="index.php" class="clear-filters">Clear Filters</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="products-grid">
        <?php if(empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <?php foreach($products as $product): ?>
                <div class="product">
                    <img src="images/products/<?= htmlspecialchars($product['product_image']) ?>"
                         alt="<?= htmlspecialchars($product['product_name']) ?>"
                         class="product-image"
                         onerror="this.src='images/products/default.png'">
                    <h2 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h2>
                    <p class="product-price">$<?= number_format($product['product_price'], 2) ?></p>
                    <p class="stock-info">Available Stock: <?= htmlspecialchars($product['product_stock']) ?></p>
                    <form action="actions/addtocart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <select name="size" required>
                            <option value="Medium">Medium</option>
                            <option value="Large">Large</option>
                        </select>
                        <select name="color" required>
                            <option value="White">White</option>
                            <option value="Black">Black</option>
                        </select>
                        <button type="submit" name="addtocart" class="btn btn-secondary">Acquire Uniform</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
#store-page {
    padding: 2rem;
}

.page-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.filter-section {
    margin-bottom: 2rem;
}

.filter-form {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.product {
    background: var(--accent-bg);
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-image {
    max-width: 100%;
    height: auto;
}

.product-name {
    font-size: 1.2rem;
    margin: 0.5rem 0;
}

.product-price {
    font-size: 1.1rem;
    color: var(--text-secondary);
}

.stock-info {
    margin-bottom: 1rem;
}

.clear-filters {
    margin-left: 1rem;
    color: var(--accent-color);
    text-decoration: underline;
}
</style>

<?php include('includes/footer.php'); ?>
