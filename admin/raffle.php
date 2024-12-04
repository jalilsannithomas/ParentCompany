<?php
session_start();
require_once('../settings/security.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include_once("../controllers/raffle_controller.php");
$entries = json_decode(get_all_entries_ctr(), true);
if ($entries === null && json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    $entries = array();
}
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

        .loading-card {
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            margin-top: 1rem;
        }

        .loading-card h3 {
            color: #fff;
            margin-bottom: 1rem;
        }

        .loading-card p {
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
                                    <td><?= htmlspecialchars($entry['name']) ?></td>
                                    <td><?= htmlspecialchars($entry['phone']) ?></td>
                                    <td>@<?= htmlspecialchars($entry['instagram']) ?></td>
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

    <?php
    require_once('../classes/raffle_class.php');

    $raffle = new raffle_class();

    $entries = json_decode($raffle->get_all_entries(), true);

    if ($entries['success']) {
        echo '<div class="entries-list">';
        echo '<h2>Raffle Entries</h2>';
        echo '<ul>';
        foreach ($entries['data'] as $entry) {
            echo '<li>' . htmlspecialchars($entry['name']) . ' - ' . htmlspecialchars($entry['phone']) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="alert error">' . htmlspecialchars($entries['message']) . '</div>';
    }
    ?>

    <script>
    function exportEntries() {
        window.location.href = '../actions/admin_export_raffle.php';
    }

    function selectWinner() {
        const modal = document.getElementById('winner-modal');
        const detailsDiv = document.getElementById('winner-details');
        
        // Show loading state
        detailsDiv.innerHTML = `
            <div class="loading-card">
                <h3>🔄 Selecting Winner...</h3>
                <p>Please wait...</p>
            </div>
        `;
        modal.style.display = 'block';

        // Use absolute path
        fetch('/ReThread_Collective/actions/admin_select_winner.php', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
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
        })
        .catch(error => {
            console.error('Error:', error);
            detailsDiv.innerHTML = `
                <div class="error-card">
                    <h3>⚠️ Error</h3>
                    <p>Failed to communicate with the server. Please check:</p>
                    <ul>
                        <li>Your internet connection</li>
                        <li>That you're still logged in</li>
                        <li>That the server is running</li>
                    </ul>
                    <p class="error-details">Error: ${error.message}</p>
                </div>
            `;
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