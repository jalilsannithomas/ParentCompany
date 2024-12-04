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
    <title>Products - Parent Company Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include('includes/sidebar.php'); ?>
Copy    <div class="admin-content">
        <div class="admin-header">
            <h1>Products</h1>
            <button class="btn-primary" onclick="document.getElementById('add-product-modal').style.display='block'">
                Add New Product
            </button>
        </div>

        <?php
        if(isset($_GET['success'])) {
            $successMessage = $_GET['success'] === 'add' ? 'Product added successfully' : 'Product updated successfully';
            echo "<div class='alert success'>{$successMessage}</div>";
        } elseif(isset($_GET['error'])) {
            switch($_GET['error']) {
                case 'image_upload':
                    $errorMessage = 'There was an error uploading the product image. Please make sure the file is in a valid image format (JPG, JPEG, or PNG) and try again.';
                    break;
                case 'invalid_image':
                    $errorMessage = 'Invalid image file type. Please upload a valid image in JPG, JPEG, or PNG format.';
                    break;
                case 'update_failed':
                    $errorMessage = 'Failed to update the product. Please try again.';
                    break;
                case 'delete':
                    $errorMessage = 'Failed to delete the product. It may be referenced in orders or cart.';
                    break;
                case 'not_found':
                    $errorMessage = 'Product not found. It may have been already deleted.';
                    break;
                default:
                    $errorMessage = 'An error occurred. Please try again.';
                    break;
            }
            echo "<div class='alert error'>{$errorMessage}</div>";
        }
        ?>

        <div class="admin-section">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="../<?= $product['product_image'] ?>" 
                                         alt="<?= $product['product_name'] ?>"
                                         class="product-thumbnail">
                                </td>
                                <td><?= $product['product_name'] ?></td>
                                <td>$<?= number_format($product['product_price'], 2) ?></td>
                                <td>
                                    <span class="<?= $product['product_stock'] < 5 ? 'stock-low' : '' ?>">
                                        <?= $product['product_stock'] ?>
                                    </span>
                                </td>
                                <td><?= $product['size'] ?></td>
                                <td><?= $product['color'] ?></td>
                                <td>
                                    <button class="btn-small" 
                                            onclick="editProduct(<?= htmlspecialchars(json_encode($product)) ?>)">
                                        Edit
                                    </button>
                                    <button class="btn-small btn-danger" 
                                            onclick="confirmDelete(<?= $product['product_id'] ?>)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div id="add-product-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Product</h2>
            <span class="close" onclick="document.getElementById('add-product-modal').style.display='none'">&times;</span>
        </div>
        <form action="../actions/admin_add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="product_price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="product_stock" min="0" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Size</label>
                    <select name="size" required>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <select name="color" required>
                        <option value="White">White</option>
                        <option value="Black">Black</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="product_desc" required></textarea>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" accept="image/*" required>
            </div>
            <button type="submit" name="add_product" class="btn-primary">Add Product</button>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="edit-product-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Product</h2>
            <span class="close" onclick="document.getElementById('edit-product-modal').style.display='none'">&times;</span>
        </div>
        <form action="../actions/admin_edit_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" id="edit_product_name" required>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="product_price" id="edit_product_price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="product_stock" id="edit_product_stock" min="0" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Size</label>
                    <select name="size" id="edit_size" required>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <select name="color" id="edit_color" required>
                        <option value="White">White</option>
                        <option value="Black">Black</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="product_desc" id="edit_product_desc" required></textarea>
            </div>
            <div class="form-group">
                <label>Current Image</label>
                <img id="edit_current_image" class="preview-image" src="" alt="Current product image">
                <label>Change Image (optional)</label>
                <input type="file" name="product_image" accept="image/*">
            </div>
            <button type="submit" name="edit_product" class="btn-primary">Update Product</button>
        </form>
    </div>
</div>

<script>
function editProduct(product) {
    document.getElementById('edit_product_id').value = product.product_id;
    document.getElementById('edit_product_name').value = product.product_name;
    document.getElementById('edit_product_price').value = product.product_price;
    document.getElementById('edit_product_stock').value = product.product_stock;
    document.getElementById('edit_size').value = product.size;
    document.getElementById('edit_color').value = product.color;
    document.getElementById('edit_product_desc').value = product.product_desc;
    document.getElementById('edit_current_image').src = '../' + product.product_image;
    
    document.getElementById('edit-product-modal').style.display = 'block';
}

function confirmDelete(productId) {
    if(confirm('Are you sure you want to delete this product?')) {
        window.location.href = '../actions/admin_delete_product.php?id=' + productId;
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
</body>
</html>