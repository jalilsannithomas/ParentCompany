<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../settings/db_class.php');
include('header.php');

// Initialize error message variable
$error_msg = '';
?>

<div class="section" id="raffle-page">
    <div class="hero-section">
        <h1 class="main-title">Enter the Raffle</h1>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert success">
            Your entry has been submitted successfully!
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert error">
            <?php 
                switch($_GET['error']) {
                    case 'duplicate':
                        echo 'This phone number has already been entered.';
                        break;
                    case 'validation':
                        echo 'Please fill in all fields correctly.';
                        break;
                    case 'db':
                        echo 'There was an error saving your entry. Please try again.';
                        break;
                    case 'system':
                        echo 'A system error occurred. Please try again later.';
                        break;
                    case 'nodata':
                        echo 'No form data received. Please try again.';
                        break;
                    default:
                        echo 'There was an error submitting your entry. Please try again.';
                }
            ?>
        </div>
    <?php endif; ?>
    
    <div class="raffle-container">
        <div class="raffle-content">
            <form action="../actions/raffleentry.php" method="POST" class="raffle-form" id="raffleForm">
                <div class="form-group">
                    <input type="text" id="name" name="name" required 
                           maxlength="100" placeholder="Your Name"
                           pattern="[A-Za-z\s]{2,100}"
                           title="Please enter a valid name (2-100 characters, letters and spaces only)">
                </div>
                
                <div class="form-group">
                    <input type="tel" id="phone" name="phone" required 
                           pattern="^\+?[0-9]{10,15}$" 
                           placeholder="+233xxxxxxxxx"
                           title="Please enter a valid phone number (10-15 digits, optionally starting with +)">
                    <small>Mobile Money number for prize delivery</small>
                </div>
                
                <div class="form-group">
                    <input type="text" id="instagram" name="instagram" required 
                           pattern="@?[a-zA-Z0-9._]{1,30}" 
                           placeholder="@yourusername"
                           title="Please enter a valid Instagram handle (letters, numbers, dots, and underscores only)">
                </div>
                
                <button type="submit" name="enter_raffle" class="submit-btn">Submit Entry</button>
            </form>
        </div>

        <div class="raffle-info">
            <h2>How it Works</h2>
            <div class="info-card">
                <div class="info-item">
                    <span class="number">1</span>
                    <p>Enter your details above to join the raffle</p>
                </div>
                <div class="info-item">
                    <span class="number">2</span>
                    <p>Make sure your mobile money number is correct</p>
                </div>
                <div class="info-item">
                    <span class="number">3</span>
                    <p>Winners will be contacted via phone</p>
                </div>
                <div class="info-item">
                    <span class="number">4</span>
                    <p>Follow us on Instagram for winner announcements</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hero-section {
    padding: 4rem 2rem;
    text-align: center;
    border-bottom: 1px solid #333;
    margin-bottom: 3rem;
}

.main-title {
    font-size: 3.5rem;
    letter-spacing: 2px;
    margin-bottom: 1rem;
}

.raffle-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    padding: 2rem;
}

.raffle-form {
    background: #111;
    padding: 2rem;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group input {
    width: 100%;
    padding: 1rem;
    background: #222;
    border: 1px solid #333;
    color: #fff;
    border-radius: 4px;
}

.form-group small {
    display: block;
    color: #888;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.submit-btn {
    width: 100%;
    padding: 1rem;
    background: #fff;
    color: #000;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    font-size: 1.1rem;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.submit-btn:hover {
    opacity: 0.9;
}

.raffle-info {
    padding: 2rem;
}

.raffle-info h2 {
    margin-bottom: 2rem;
    font-size: 2rem;
}

.info-card {
    background: #111;
    padding: 2rem;
    border-radius: 8px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.number {
    width: 40px;
    height: 40px;
    background: #fff;
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.alert {
    max-width: 600px;
    margin: 0 auto 2rem;
    padding: 1rem;
    border-radius: 4px;
    text-align: center;
}

.alert.success {
    background: #00cc88;
    color: #000;
}

.alert.error {
    background: #cc0000;
    color: #fff;
}

@media (max-width: 768px) {
    .hero-section {
        padding: 2rem 1rem;
    }

    .main-title {
        font-size: 2.5rem;
    }

    .raffle-container {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 1rem;
    }
}
</style>

<script>
document.getElementById('raffleForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const instagram = document.getElementById('instagram').value.trim();
    
    let isValid = true;
    let errorMessage = '';
    
    // Validate name
    if (!name.match(/^[A-Za-z\s]{2,100}$/)) {
        isValid = false;
        errorMessage = 'Please enter a valid name (2-100 characters, letters and spaces only)';
    }
    
    // Validate phone
    if (!phone.match(/^\+?[0-9]{10,15}$/)) {
        isValid = false;
        errorMessage = 'Please enter a valid phone number (10-15 digits, optionally starting with +)';
    }
    
    // Validate Instagram
    if (!instagram.match(/^@?[a-zA-Z0-9._]{1,30}$/)) {
        isValid = false;
        errorMessage = 'Please enter a valid Instagram handle';
    }
    
    if (!isValid) {
        e.preventDefault();
        alert(errorMessage);
    }
});
</script>

<?php include('footer.php'); ?>