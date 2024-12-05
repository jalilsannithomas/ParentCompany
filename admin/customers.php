<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('../settings/security.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
header("location: ../view/login.php");
    exit();
}

include_once("../controllers/user_controller.php");
include_once("../controllers/order_controller.php");
$customers = get_all_customers_ctr();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customers - Parent Company Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include('includes/sidebar.php'); ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Customers</h1>
                <div class="search-container">
                    <div class="search-box">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                            <path fill="none" stroke="currentColor" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="customer-search" placeholder="Search customers...">
                    </div>
                </div>
            </div>

            <div class="admin-section">
                <div class="customers-grid" id="customers-list">
                    <?php foreach($customers as $customer): 
                        $orderCount = get_user_orders_count_ctr($customer['user_id']);
                    ?>
                        <div class="customer-card">
                            <div class="customer-card-header">
                                <div class="customer-avatar">
                                    <?= strtoupper(substr($customer['user_name'], 0, 1)) ?>
                                </div>
                                <div class="customer-main-info">
                                    <h3><?= $customer['user_name'] ?></h3>
                                    <p class="customer-email"><?= $customer['user_email'] ?></p>
                                </div>
                            </div>
                            <div class="customer-card-body">
                                <div class="customer-info-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                                        <path fill="currentColor" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span><?= $customer['user_phone'] ?></span>
                                </div>
                                <div class="customer-info-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                                        <path fill="currentColor" d="M16 4a1 1 0 011 1v4.2a5.5 5.5 0 012-.2c1.9 0 3 1.3 3 3s-1.1 3-3 3c-1.1 0-2.5-.6-3.8-1.8-.4.7-.9 1.4-1.5 2A7 7 0 0120 19h2v2H2v-2h2a7 7 0 016.3-7c-.6-.6-1.1-1.3-1.5-2C7.5 11.4 6.1 12 5 12c-1.9 0-3-1.3-3-3s1.1-3 3-3c.7 0 1.4.1 2 .2V5a1 1 0 011-1h8zm-1 10.2c.7-.7 1.3-1.5 1.8-2.2.7.6 1.4 1 2.2 1 .4 0 1-.2 1-.8s-.6-.8-1-.8c-.9 0-1.9-.4-2.7-1.2-.8.8-1.8 1.2-2.7 1.2-.4 0-1 .2-1 .8s.6.8 1 .8c.8 0 1.5-.4 2.2-1 .5.7 1.1 1.5 1.8 2.2a5 5 0 00-4.4 4.8h8.8a5 5 0 00-4.4-4.8zM7 10c-.4 0-1 .2-1 .8s.6.8 1 .8 1-.2 1-.8-.6-.8-1-.8zm10 0c-.4 0-1 .2-1 .8s.6.8 1 .8 1-.2 1-.8-.6-.8-1-.8z"/>
                                    </svg>
                                    <span><?= $orderCount ?> Orders</span>
                                </div>
                                <div class="customer-info-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                                        <path fill="currentColor" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                                    </svg>
                                    <span>Joined <?= date('M d, Y', strtotime($customer['created_at'])) ?></span>
                                </div>
                            </div>
                            <div class="customer-card-footer">
                                <button class="btn-icon" onclick="viewCustomerDetails(<?= $customer['user_id'] ?>)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                                        <path fill="none" stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path fill="none" stroke="currentColor" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Orders
                                </button>
                                <button class="btn-icon" onclick="editCustomer(<?= $customer['user_id'] ?>, '<?= htmlspecialchars($customer['user_name'], ENT_QUOTES) ?>', '<?= $customer['user_email'] ?>', '<?= $customer['user_phone'] ?>')">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                                        <path fill="none" stroke="currentColor" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    Edit
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div id="customer-details-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Customer Details</h2>
                <button class="close-btn" onclick="closeCustomerModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M6.225 4.811a1 1 0 00-1.414 1.414L10.586 12 4.81 17.775a1 1 0 101.414 1.414L12 13.414l5.775 5.775a1 1 0 001.414-1.414L13.414 12l5.775-5.775a1 1 0 00-1.414-1.414L12 10.586 6.225 4.81z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="customer-info" class="customer-details-section"></div>
                <div id="customer-orders" class="customer-orders-section"></div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div id="edit-customer-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Customer</h2>
                <button class="close-btn" onclick="closeEditModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M6.225 4.811a1 1 0 00-1.414 1.414L10.586 12 4.81 17.775a1 1 0 101.414 1.414L12 13.414l5.775 5.775a1 1 0 001.414-1.414L13.414 12l5.775-5.775a1 1 0 00-1.414-1.414L12 10.586 6.225 4.81z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-customer-form" class="form-grid">
                    <input type="hidden" id="edit-user-id" name="user_id">
                    <div class="form-group">
                        <label for="edit-name">Name</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" id="edit-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="tel" id="edit-phone" name="phone" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include jQuery if needed -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    function viewCustomerDetails(userId) {
        fetch(`../actions/admin_get_customer_details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('customer-info').innerHTML = formatCustomerInfo(data.customer);
                document.getElementById('customer-orders').innerHTML = formatCustomerOrders(data.orders);
                document.getElementById('customer-details-modal').style.display = 'block';
            }
        });
    }

    function formatCustomerInfo(customer) {
        return `
            <div class="customer-info-card">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> ${customer.user_name}</p>
                <p><strong>Email:</strong> ${customer.user_email}</p>
                <p><strong>Phone:</strong> ${customer.user_phone}</p>
                <p><strong>Member Since:</strong> ${new Date(customer.created_at).toLocaleDateString()}</p>
            </div>
        `;
    }

    function formatCustomerOrders(orders) {
        if (!orders.length) {
            return '<p>No orders found.</p>';
        }

        return `
            <div class="customer-orders">
                <h3>Order History</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${orders.map(order => `
                            <tr>
                                <td>#${order.order_id}</td>
                                <td>${new Date(order.created_at).toLocaleDateString()}</td>
                                <td>$${parseFloat(order.order_amount).toFixed(2)}</td>
                                <td><span class="status-badge status-${order.order_status}">
                                    ${order.order_status}
                                </span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    function editCustomer(userId, name, email, phone) {
        document.getElementById('edit-user-id').value = userId;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-phone').value = phone;
        document.getElementById('edit-customer-modal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('edit-customer-modal').style.display = 'none';
    }

    // Handle form submission
    document.getElementById('edit-customer-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Password validation
        const password = document.getElementById('edit-password').value;
        const confirmPassword = document.getElementById('edit-confirm-password').value;
        
        if (password && password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }
        
        if (password && password.length < 8) {
            alert('Password must be at least 8 characters long');
            return;
        }
        
        const formData = new FormData(this);
        
        fetch('../actions/admin_edit_customer.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Customer information updated successfully');
                location.reload();
            } else {
                alert('Error updating customer information: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating customer information');
        });
    });

    // Enhanced search functionality
    document.getElementById('customer-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const customerCards = document.querySelectorAll('.customer-card');
        
        customerCards.forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const email = card.querySelector('.customer-email').textContent.toLowerCase();
            const phone = card.querySelector('.customer-info-item span').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Close modal when clicking the close button
    document.querySelector('.close-btn').addEventListener('click', function() {
        document.getElementById('customer-details-modal').style.display = 'none';
    });
    </script>
</body>
</html>