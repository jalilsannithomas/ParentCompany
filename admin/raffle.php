<?php
session_start();
require_once('../settings/security.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include_once("../controllers/raffle_controller.php");
$entries_json = get_all_entries_ctr();
$entries_data = json_decode($entries_json, true);
$entries = [];

if ($entries_data && isset($entries_data['success']) && $entries_data['success']) {
    $entries = $entries_data['data'];
} else {
    error_log("Error fetching entries: " . $entries_json);
    $entries = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raffle Entries - Parent Company Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .raffle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .raffle-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .raffle-card h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.2rem;
        }

        .raffle-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .raffle-info label {
            font-weight: bold;
            color: #666;
        }

        .raffle-info span {
            color: #333;
        }

        .raffle-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

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
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .close {
            color: #666;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 10px;
        }

        .close:hover {
            color: #333;
        }

        .modal-header {
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
            margin-bottom: 1rem;
        }

        .modal-header h2 {
            color: #333;
            margin: 0;
        }

        .search-box {
            margin-bottom: 1.5rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .search-box input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .no-entries {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
            color: #666;
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
                        <p class="stat-number"><?= count($entries) ?></p>
                    </div>
                </div>

                <div class="search-box">
                    <input type="text" id="entry-search" placeholder="Search entries by name, phone, or Instagram handle...">
                </div>

                <div class="raffle-grid" id="entries-grid">
                    <?php if (empty($entries)): ?>
                        <div class="no-entries">
                            <h3>No Raffle Entries</h3>
                            <p>There are currently no entries in the raffle.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($entries as $entry): ?>
                            <div class="raffle-card" data-entry-id="<?= $entry['entry_id'] ?>" data-search="<?= strtolower($entry['name'] . ' ' . $entry['phone'] . ' ' . $entry['instagram']) ?>">
                                <h3>Entry #<?= $entry['entry_id'] ?></h3>
                                <div class="raffle-info">
                                    <label>Name:</label>
                                    <span><?= htmlspecialchars($entry['name']) ?></span>
                                    
                                    <label>Phone:</label>
                                    <span><?= htmlspecialchars($entry['phone']) ?></span>
                                    
                                    <label>Instagram:</label>
                                    <span>@<?= htmlspecialchars($entry['instagram']) ?></span>
                                    
                                    <label>Date:</label>
                                    <span><?= date('M d, Y', strtotime($entry['created_at'])) ?></span>
                                </div>
                                <div class="raffle-actions">
                                    <button class="btn-small btn-danger" 
                                            onclick="deleteEntry(<?= $entry['entry_id'] ?>)">
                                        Delete Entry
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Search functionality
    document.getElementById('entry-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const entries = document.querySelectorAll('.raffle-card');
        
        entries.forEach(entry => {
            const searchData = entry.getAttribute('data-search').toLowerCase();
            if (searchData.includes(searchTerm)) {
                entry.style.display = '';
            } else {
                entry.style.display = 'none';
            }
        });
    });

    // Close modal when clicking the X
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('winner-modal').style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('winner-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    function deleteEntry(entryId) {
        if (confirm('Are you sure you want to delete this entry?')) {
            const formData = new FormData();
            formData.append('entry_id', entryId);
            
            fetch('../actions/admin_delete_entry.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove the entry card from the UI
                    const entryCard = document.querySelector(`.raffle-card[data-entry-id="${entryId}"]`);
                    if (entryCard) {
                        entryCard.remove();
                        
                        // Update total entries count
                        const totalEntriesElement = document.querySelector('.stat-number');
                        if (totalEntriesElement) {
                            const currentCount = parseInt(totalEntriesElement.textContent);
                            totalEntriesElement.textContent = currentCount - 1;
                        }
                    }
                } else {
                    alert('Error deleting entry: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the entry');
            });
        }
    }

    function exportEntries() {
        window.location.href = '../actions/admin_export_raffle.php';
    }

    function selectWinner() {
        const modal = document.getElementById('winner-modal');
        const detailsDiv = document.getElementById('winner-details');
        
        // Show loading state
        detailsDiv.innerHTML = '<div class="loading-card"><h3>Selecting Winner...</h3><p>Please wait...</p></div>';
        modal.style.display = 'block';
        
        fetch('../actions/admin_select_winner.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.winner) {
                detailsDiv.innerHTML = `
                    <div class="winner-card">
                        <h3>🎉 Congratulations! 🎉</h3>
                        <p><strong>Name:</strong> ${data.winner.name}</p>
                        <p><strong>Phone:</strong> ${data.winner.phone}</p>
                        <p><strong>Instagram:</strong> @${data.winner.instagram}</p>
                        <p><strong>Entry Date:</strong> ${new Date(data.winner.created_at).toLocaleDateString()}</p>
                    </div>
                `;
            } else {
                detailsDiv.innerHTML = `
                    <div class="error-card">
                        <h3>Error</h3>
                        <p>${data.message || 'No entries available'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            detailsDiv.innerHTML = `
                <div class="error-card">
                    <h3>Error</h3>
                    <p>Failed to select winner. Please try again.</p>
                </div>
            `;
        });
    }
    </script>
</body>
</html>