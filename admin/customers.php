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
                <div class="search-box">
                    <input type="text" id="customer-search" placeholder="Search customers...">
                </div>
            </div>

            <div class="admin-section">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Orders</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customers-list">
                            <?php foreach($customers as $customer): ?>
                                <tr>
                                    <td><?= $customer['user_name'] ?></td>
                                    <td><?= $customer['user_email'] ?></td>
                                    <td><?= $customer['user_phone'] ?></td>
                                    <td><?= get_user_orders_count_ctr($customer['user_id']) ?></td>
                                    <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                                    <td>
                                        <button class="btn-small" 
                                                onclick="viewCustomerDetails(<?= $customer['user_id'] ?>)">
                                            View Orders
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

    <!-- Customer Details Modal -->
    <div id="customer-details-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Customer Details</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="customer-info"></div>
                <div id="customer-orders"></div>
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

    // Search functionality
    document.getElementById('customer-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#customers-list tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Close modal when clicking the close button
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('customer-details-modal').style.display = 'none';
    });
    </script>
</body>
</html>