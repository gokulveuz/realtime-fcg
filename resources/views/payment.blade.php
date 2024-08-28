<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment</title>
</head>

<body>
    <form action="{{ route('request.upi.payment') }}" method="POST">
        @csrf
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount" required>

        <label for="vpa">UPI VPA:</label>
        <input type="text" id="vpa" value="gokulsuresh188-1@okaxis" name="vpa" required>

        <button type="submit">Pay Now</button>
    </form>

    {{-- <div class="card card-default">
        <div class="card-body text-center">
            <form action="{{ route('razorpay.payment.store') }}" method="POST">
                @csrf
                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ env('RAZORPAY_KEY') }}" data-amount="10000"
                    data-buttontext="Pay 100 INR" data-name="GeekyAnts official" data-description="Razorpay payment"
                    data-image="/images/logo-icon.png" data-prefill.name="ABC" data-prefill.email="abc@gmail.com"
                    data-theme.color="#ff7529"></script>
            </form>
            <button class="btn btn-success" onclick="payNow()">payNow</button>
        </div>
    </div> --}}

</body>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function payNow() {
        fetch('/payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    amount: 10000,
                    name: "ABC",
                    email: "abc@gmail.com"
                })
            })
            .then(response => response.json())
            .then(data => {
                var options = {
                    "key": data.razorpay_key,
                    "amount": data.amount,
                    "currency": "INR",
                    "name": data.name,
                    "description": "Razorpay payment",
                    "image": "/images/logo-icon.png",
                    "order_id": data.order_id,
                    "handler": function(response) {
                        // Handle the payment response here
                        alert("Payment successful!");
                    },
                    "prefill": {
                        "name": data.name,
                        "email": data.email
                    },
                    "theme": {
                        "color": "#ff7529"
                    }
                };
                var rzp = new Razorpay(options);
                rzp.open();
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>

</html>
