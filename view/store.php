<?php include('layouts/header.php'); ?>

<div class="section active" id="store-page">
    <h1>Parent Company ©</h1>
    
    <?php
    include("../controllers/product_controller.php");
    $products = get_all_products_ctr();
    ?>

    <div class="product-grid">
        <?php foreach($products as $product): ?>
            <div class="product-card">
                <img src="<?= $product['product_image'] ?>" class="rotating-image">
                <div class="product-name"><?= $product['product_name'] ?></div>
                <div class="product-price">$<?= number_format($product['product_price'], 2) ?></div>
                <div class="stock-info">Available Stock: <?= $product['product_stock'] ?></div>
                
                <form action="../actions/add_to_cart_action.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    <select name="size" required>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                    </select>
                    <select name="color" required>
                        <option value="White">White</option>
                        <option value="Black">Black</option>
                    </select>
                    <button type="submit" name="add_to_cart">Acquire Uniform</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('layouts/footer.php'); ?>
