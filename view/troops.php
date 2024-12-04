<?php 
// Add logs for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>upd<?php 
include("header.php");
?>

<div class="troops-page container"> <!-- Added container class for styling -->
    <div class="troops-content grid-3"> <!-- Added grid class for styling -->
    <h1>Troops</h1>
    <div class="troops-content">
        <div class="troop-card">
            <img src="../images/troop.jpg" alt="Troop Member">
            <div class="troop-info">
                <h3>Join The Movement</h3>
                <p>Be part of something bigger than yourself.</p>
                <button onclick="location.href='register.php'">Enlist Now</button>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>