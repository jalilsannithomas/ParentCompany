<?php
include('header.php');
?>

<div class="contact-page">
    <h1 class="page-title">Contact Support</h1>

    <div class="contact-info">
        <h2>Support Contact</h2>
        <p>Phone: <a href="tel:+233553379580">+233 55 337 9580</a></p>
        <p>Email: <a href="mailto:support@parentcompany.com">support@parentcompany.com</a></p>
    </div>

    <div class="contact-instructions">
        <h2>Reporting Issues</h2>
        <p>When contacting support, please provide a detailed description of the issue you are experiencing, including any relevant information or error messages. This will help us assist you more effectively.</p>
    </div>
</div>

<style>
.contact-page {
    padding: 2rem;
    max-width: 800px;
    margin: 0 auto;
    background-color: var(--secondary-bg);
    color: var(--text-primary);
}

.contact-info, .contact-instructions {
    background-color: var(--accent-bg);
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    margin-top: 0;
}

p {
    margin-bottom: 1rem;
}
</style>

<?php
include('footer.php');
?>
