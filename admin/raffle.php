<?php
session_start();
require_once('../settings/security.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include_once("../controllers/raffle_controller.php");
$entries = get_all_entries_ctr();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raffle Entries - Parent Company Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .winner-card {
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            margin-top: 1rem;
        }

        .winner-card h3 {
            color: #fff;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .winner-card p {
            margin: 1rem 0;
            color: #fff;
            font-size: 1.1rem;
        }

        .winner-card strong {
            color: #fff;
            font-weight: bold;
        }

        .error-card {
            background: #ff4444;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            margin-top: 1rem;
        }

        .error-card h3 {
            color: #fff;
            margin-bottom: 1rem;
        }

        .error-card p {
            color: #fff;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }

        .modal-content {
            background-color: #222;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 10px;
        }

        .close:hover {
            color: #fff;
        }

        .modal-header {
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
            margin-bottom: 1rem;
        }

        .modal-header h2 {
            color: #fff;
            margin: 0;
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include('includes/sidebar.php'); ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Raffle Entries</h1>
                <div class="action-buttons">
                    <button onclick="exportEntries()" class="btn-primary">Export to CSV</button>
                    <button onclick="selectWinner()" class="btn-primary">Select Winner</button>
                </div>
            </div>

            <div class="admin-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Entries</h3>
                        <p class="stat-number"><?= get_entry_count_ctr() ?></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Instagram</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($entries as $entry): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($entry['created_at'])) ?></td>
                                    <td><?= $entry['name'] ?></td>
                                    <td><?= $entry['phone'] ?></td>
                                    <td>@<?= $entry['instagram'] ?></td>
                                    <td>
                                        <button class="btn-small btn-danger" 
                                                onclick="deleteEntry(<?= $entry['entry_id'] ?>)">
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

    <!-- Winner Selection Modal -->
    <div id="winner-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Raffle Winner</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="winner-details"></div>
            </div>
        </div>
    </div>

    <script>
    function exportEntries() {
        window.location.href = '../actions/admin_export_raffle.php';
    }

    function selectWinner() {
        fetch('../actions/admin_select_winner.php')
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('winner-modal');
            const detailsDiv = document.getElementById('winner-details');
            
            if(data.success) {
                detailsDiv.innerHTML = `
                    <div class="winner-card">
                        <h3>🎉 Selected Winner:</h3>
                        <p><strong>Name:</strong> ${data.winner.name}</p>
                        <p><strong>Phone:</strong> ${data.winner.phone}</p>
                        <p><strong>Instagram:</strong> @${data.winner.instagram}</p>
                        <p><strong>Entry Date:</strong> ${new Date(data.winner.created_at).toLocaleDateString()}</p>
                    </div>
                `;
            } else {
                detailsDiv.innerHTML = `
                    <div class="error-card">
                        <h3>⚠️ Error</h3>
                        <p>${data.message || 'Failed to select winner. Please try again.'}</p>
                    </div>
                `;
            }
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            const modal = document.getElementById('winner-modal');
            const detailsDiv = document.getElementById('winner-details');
            detailsDiv.innerHTML = `
                <div class="error-card">
                    <h3>⚠️ Error</h3>
                    <p>Failed to communicate with the server. Please try again.</p>
                </div>
            `;
            modal.style.display = 'block';
        });
    }

    // Close modal when clicking on X or outside the modal
    document.querySelector('.close').onclick = function() {
        document.getElementById('winner-modal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('winner-modal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    function deleteEntry(entryId) {
        if(confirm('Are you sure you want to delete this entry?')) {
            fetch('../actions/admin_delete_entry.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    entry_id: entryId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    }
    </script>
</body>
</html>