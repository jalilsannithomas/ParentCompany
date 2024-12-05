<?php
session_start();
require_once('../settings/security.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include_once("../controllers/product_controller.php");
$products = get_all_products_ctr();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Parent Company Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include('includes/sidebar.php'); ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Products</h1>
                <button class="btn-primary" onclick="document.getElementById('add-product-modal').style.display='block'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add New Product
                </button>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert success">
                    <?php echo $_GET['success'] === 'add' ? 'Product added successfully' : 'Product updated successfully'; ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert error">
                    <?php
                    switch($_GET['error']) {
                        case 'image_upload':
                            echo 'There was an error uploading the product image. Please make sure the file is in a valid image format (JPG, JPEG, or PNG) and try again.';
                            break;
                        case 'invalid_image':
                            echo 'Invalid image file type. Please upload a valid image in JPG, JPEG, or PNG format.';
                            break;
                        case 'update_failed':
                            echo 'Failed to update the product. Please try again.';
                            break;
                        case 'delete':
                            echo 'Failed to delete the product. It may be referenced in orders or cart.';
                            break;
                        case 'not_found':
                            echo 'Product not found. It may have been already deleted.';
                            break;
                        default:
                            echo 'An error occurred. Please try again.';
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <div class="product-meta">
                                <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                <span class="stock <?php echo $product['product_stock'] < 5 ? 'stock-low' : ''; ?>">
                                    <?php echo $product['product_stock']; ?> in stock
                                </span>
                            </div>
                            <div class="product-attributes">
                                <span class="attribute">
                                    <strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?>
                                </span>
                                <span class="attribute">
                                    <strong>Color:</strong> 
                                    <span class="color-dot" style="background-color: <?php echo htmlspecialchars($product['color']); ?>"></span>
                                    <?php echo htmlspecialchars($product['color']); ?>
                                </span>
                            </div>
                            <div class="product-actions">
                                <button class="btn-edit" onclick='editProduct(<?php echo json_encode($product); ?>)'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                                    </svg>
                                    Edit
                                </button>
                                <button class="btn-delete" onclick="confirmDelete(<?php echo $product['product_id']; ?>)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Product Modal -->
            <div id="add-product-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Product</h2>
                        <button class="modal-close" onclick="document.getElementById('add-product-modal').style.display='none'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="../actions/add_product.php" method="POST" enctype="multipart/form-data" class="product-form">
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="product_price">Price (GH₵)</label>
                                <input type="number" id="product_price" name="product_price" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_stock">Stock</label>
                                <input type="number" id="product_stock" name="product_stock" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="size">Size</label>
                                <select id="size" name="size" required>
                                    <option value="">Select Size</option>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="color">Color</label>
                                <input type="color" id="color" name="color" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="product_image">Product Image</label>
                            <input type="file" id="product_image" name="product_image" accept="image/*" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="document.getElementById('add-product-modal').style.display='none'">Cancel</button>
                            <button type="submit" class="btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div id="edit-product-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Edit Product</h2>
                        <button class="modal-close" onclick="document.getElementById('edit-product-modal').style.display='none'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <form action="../actions/update_product.php" method="POST" enctype="multipart/form-data" class="product-form">
                        <input type="hidden" id="edit_product_id" name="product_id">
                        
                        <div class="form-group">
                            <label for="edit_product_name">Product Name</label>
                            <input type="text" id="edit_product_name" name="product_name" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_product_price">Price (GH₵)</label>
                                <input type="number" id="edit_product_price" name="product_price" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_product_stock">Stock</label>
                                <input type="number" id="edit_product_stock" name="product_stock" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_size">Size</label>
                                <select id="edit_size" name="size" required>
                                    <option value="">Select Size</option>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_color">Color</label>
                                <input type="color" id="edit_color" name="color" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_product_image">Product Image (Leave empty to keep current image)</label>
                            <input type="file" id="edit_product_image" name="product_image" accept="image/*">
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="document.getElementById('edit-product-modal').style.display='none'">Cancel</button>
                            <button type="submit" class="btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    function editProduct(product) {
        document.getElementById('edit_product_id').value = product.product_id;
        document.getElementById('edit_product_name').value = product.product_name;
        document.getElementById('edit_product_price').value = product.product_price;
        document.getElementById('edit_product_stock').value = product.product_stock;
        document.getElementById('edit_size').value = product.size;
        document.getElementById('edit_color').value = product.color;
        
        document.getElementById('edit-product-modal').style.display = 'block';
    }

    function confirmDelete(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            window.location.href = `../actions/delete_product.php?product_id=${productId}`;
        }
    }
    </script>
</body>
</html>