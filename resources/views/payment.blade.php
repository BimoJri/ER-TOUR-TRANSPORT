<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="SB-Mid-client-uFG0YRcfY_bZjJGl">
    </script>
</head>
<body>
    <h1>Payment</h1>
    <form id="payment-form">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <input type="number" name="amount" placeholder="Amount" required>
        <button type="button" id="pay-button">Pay Now</button>
    </form>

    <script>
        document.getElementById('pay-button').onclick = function () {
            // Ambil data form
            const form = document.getElementById('payment-form');
            const formData = new FormData(form);

            // Kirim data ke server untuk mendapatkan Snap Token
            fetch('/payment/checkout', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Panggil pop-up pembayaran Midtrans
                snap.pay(data.snapToken);
            })
            .catch(error => console.error('Error:', error));
        };
    </script>
</body>
</html>
