// ReThread Collective Payment Integration - Live
var paymentForm = document.getElementById('paymentForm');

function payWithPaystack(e) {
    e.preventDefault();
    console.log('Initializing Paystack payment...');
    
    var handler = PaystackPop.setup({
        key: 'pk_live_8184a403cf27a3843ce2d7764dfab31ad4ad8bbb', // Live public key
        email: document.getElementById('email-address').value,
        amount: document.getElementById('amount').value * 100,
        currency: 'GHS',
        ref: "" + Math.floor(Math.random() * 1000000000 + 1),
        callback: function(response) {
            console.log('Payment completed. Reference: ' + response.reference);
            $.ajax({
                url: "../process.php",
                method: "POST",
                data: {
                    reference: response.reference
                },
                success: function (response) {
                    try {
                        console.log('Server response:', response);
                        var data = JSON.parse(response);
                        if (data.success) {
                            window.location.href = "success.php";
                        } else {
                            alert('Error processing order: ' + data.message);
                            window.location.href = "checkout.php?error=" + encodeURIComponent(data.message);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Error processing payment. Please contact support.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error processing payment. Please contact support.');
                    window.location.href = "checkout.php?error=ajax_error";
                }
            });
        },
        onClose: function() {
            alert('Transaction was not completed, window closed.');
        }
    });
    handler.openIframe();
}

// Add event listener after function definition
paymentForm.addEventListener('submit', payWithPaystack, false);
