<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use URWay\Client;
use Alaaelsaid\LaravelUrwayPayment\Facade\Urway;


class PaymentController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function index()
    {
        // $upiId = 'gokulsuresh188-1@okaxis';
        // $amount = '100';
        // $name = 'Gokul K S';

        // $upiPaymentString = "upi://pay?pa=$upiId&pn=$name&am=$amount&cu=INR";

        // // Generate QR code
        // $qrCode = QrCode::size(300)->generate($upiPaymentString);

        // // Return QR code view
        // return view('upi-qr-code', compact('qrCode'));


        return view('payment');
    }

    public function processPayment(Request $request)
    {


        $client = new Client();

        $client->setTrackId('YOUR_TRAKING_ID')
            ->setCustomerEmail('gokulveuz@gmail.com')
            ->setCustomerIp('...')
            ->setCurrency('USD')
            ->setCountry('EG')
            ->setAmount(5)
            ->setRedirectUrl('http://localhost:8000/callback');



        $redirect_url = $client->pay();

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $amount = 10000;

        // Create an order in Razorpay
        $order = $api->order->create([
            'amount' => $amount, // Amount in paise
            'currency' => 'INR',
            'payment_capture' => 1 // Auto capture
        ]);

        // Return the necessary data to the frontend
        return response()->json([
            'order_id' => $order['id'],
            'razorpay_key' => env('RAZORPAY_KEY'),
            'amount' => $amount,
            'name' => 'ABC', // Prefill data or fetch from DB
            'email' => 'abc@gmail.com', // Prefill data or fetch from DB
        ]);
    }

    public function payTabs()
    {
        $response = Http::withHeaders([
            'Authorization' => env('PAYTABS_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('PAYTABS_BASE_URL') . '/payment/request', [
            'profile_id' => env('PAYTABS_PROFILE_ID'),
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'cart_id' => '4244b9fd-c7e9-4f16-8d3c-4fe7bf6c48ca',
            'cart_description' => 'Dummy Order 35925502061445345',
            'cart_currency' => 'INR',
            'cart_amount' => 46.17,
            'callback' => 'http://127.0.0.1:8001/callback',
            'return' => 'http://127.0.0.1:8001/callback',
            'customer_details' => [
                'name' => 'first last',
                'email' => 'email@domain.com',
                'phone' => '0522222222',
                'street1' => 'address street',
                'city' => 'dubai',
                'state' => 'du',
                'country' => 'AE',
                'zip' => '12345'
            ],
            'shipping_details' => [
                'name' => 'name1 last1',
                'email' => 'email1@domain.com',
                'phone' => '971555555555',
                'street1' => 'street2',
                'city' => 'dubai',
                'state' => 'dubai',
                'country' => 'AE',
                'zip' => '54321'
            ],
        ]);

        $result = $response->json();

        return $result;
    }


    public function generateUpiQrCode(Request $request)
    {
        $upiId = 'gokulsuresh188-1@okaxis';
        $amount = '100';
        $name = 'Gokul K S';

        $upiPaymentString = "upi://pay?pa=$upiId&pn=$name&am=$amount&cu=INR";

        // Generate QR code
        $qrCode = QrCode::size(300)->generate($upiPaymentString);

        // Return QR code view
        return view('upi-qr-code', compact('qrCode'));
    }

    public function confirmPayment(Request $request) {}


    public function createUPIPaymentRequest(Request $request)
    {


        $res  = $this->standardPaymentLink();


        dd($res);

        $amount = $request->input('amount');
        $vpa = $request->input('vpa');
        $amountInPaisa = $amount * 100;
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));


        // Create a payment request
        try {
            $payment = $api->payment->create([
                'amount' => $amountInPaisa,
                'currency' => 'INR',
                'method' => 'upi',
                'vpa' => $vpa,
                'description' => 'Payment for your order',
                'notes' => [
                    'order_id' => 'order_' . uniqid(),
                ],
            ]);

            return redirect()->away($payment->short_url);
        } catch (\Exception $e) {
            dd($e);
            return back()->withErrors('Error: ' . $e->getMessage());
        }


        // // Retrieve payment details
        // $amount = $request->input('amount'); // Amount in INR (e.g., 100.00)
        // $vpa = $request->input('vpa'); // UPI VPA (e.g., someone@upi)

        // // Convert the amount to paisa (100 INR = 10000 paisa)
        // $amountInPaisa = $amount * 100;

        // // Initialize Razorpay API with your credentials
        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // try {
        //     // Create an order
        //     $order = $api->order->create([
        //         'amount' => $amountInPaisa,
        //         'currency' => 'INR',
        //         'payment_capture' => 1, // Auto capture payment
        //     ]);

        //     dd($order);

        //     // Generate UPI deep link
        //     $upiLink = "upi://pay?pa=$vpa&pn=YourCompanyName&am=$amount&cu=INR&url=https://yourcallbackurl.com&tr=" . $order['id'];

        //     // Redirect user to UPI app (Google Pay, PhonePe, etc.)
        //     return redirect()->away($upiLink);

        // } catch (\Exception $e) {
        //     return back()->withErrors('Error: ' . $e->getMessage());
        // }


        // // // Retrieve the payment details from the request
        // // $amount = $request->input('amount'); // Amount in INR (e.g., 100.00)
        // // $vpa = $request->input('vpa'); // UPI VPA (e.g., someone@upi)

        // // // Convert the amount to paisa (100 INR = 10000 paisa)
        // // $amountInPaisa = $amount * 100;

        // // // Initialize Razorpay API with your credentials
        // // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // // try {
        // //     // Create an order
        // //     $order = $api->order->create([
        // //         'amount' => $amountInPaisa, // Amount in paisa
        // //         'currency' => 'INR',
        // //         'payment_capture' => 1, // Auto capture payment
        // //     ]);

        // //     // Create a payment with UPI method
        // //     $payment = $api->payment->create([
        // //         'amount' => $amountInPaisa, // Amount in paisa
        // //         'currency' => 'INR',
        // //         'order_id' => $order['id'], // Use the order ID created earlier
        // //         'method' => 'upi',
        // //         'vpa' => $vpa, // UPI VPA provided by the user
        // //         'description' => 'Payment for Order ID: ' . $order['id'],
        // //     ]);

        // //     // Capture the payment
        // //     $capturedPayment = $payment->capture();

        // //     // Handle the successful payment
        // //     if ($capturedPayment->status === 'captured') {
        // //         // Update your order status in the database
        // //         return response()->json([
        // //             'status' => 'success',
        // //             'payment_id' => $capturedPayment->id,
        // //             'order_id' => $order['id'],
        // //         ]);
        // //     } else {
        // //         return response()->json([
        // //             'status' => 'failure',
        // //             'message' => 'Payment not captured.',
        // //         ], 400);
        // //     }
        // // } catch (\Exception $e) {
        // //     return response()->json([
        // //         'status' => 'error',
        // //         'message' => $e->getMessage(),
        // //     ], 500);
        // }



        // // Retrieve the payment details from the request
        // $amount = $request->input('amount'); // Amount in INR (e.g., 100.00)

        // // Convert the amount to paisa (100 INR = 10000 paisa)
        // $amountInPaisa = $amount * 100;

        // // Initialize Razorpay API with your credentials
        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // try {
        //     // Create an order
        //     $order = $api->order->create([
        //         'amount' => $amountInPaisa, // amount in paisa
        //         'currency' => 'INR',
        //         'payment_capture' => 1, // auto capture payment
        //     ]);

        //     // Pass the order ID to the payment view
        //     return view('payment', ['orderId' => $order->id]);
        // } catch (\Exception $e) {
        //     return back()->withErrors('Error: ' . $e->getMessage());
        // }

    }

    public function  standardPaymentLink()
    {
        //  STANDARD PAYMENT LINK
        return  $this->api->paymentLink->create(array(
            'amount' => 5  * 100,
            'currency' => 'INR',
            'accept_partial' => true,
            'first_min_partial_amount' => 100,
            'expire_by' => now()->addMinutes(20)->timestamp,
            'reference_id' => 'TS198912232323',
            'description' => 'For XYZ purpose',
            'customer' => array(
                'name' => 'Gokul k s',
                'email' => 'websoulgokulks@gmail.com',
                'contact' => '+916374081480'
            ),
            'notify' => array('sms' => true, 'email' => true),
            'reminder_enable' => true,
            // 'notes' => array('policy_name' => 'Jeevan Bima'),
            'callback_url' => 'http://127.0.0.1:8000/callback',
            'callback_method' => 'get'
        ));
    }

    public function createCustomer()
    {
        return  $this->api->customer->create(array(
            'name' => 'Razorpay User',
            'email' => 'customer@razorpay.com',
            'contact' => '9123456780',
            'fail_existing' => '0',
            'notes' => array('notes_key_1' => 'Tea, Earl Grey, Hot', 'notes_key_2' => 'Tea, Earl Grey… decaf')
        ));
    }

    public function createOrder()
    {
        return $this->api->order->create(array(
            'amount' => 0,
            'currency' => 'INR',
            'method' => 'upi',
            'customer_id' => 'cust_4xbQrmEoA5WJ01',
            'token' => array('max_amount' => 200000, 'expire_at' => 2709971120, 'frequency' => 'monthly'),
            'receipt' => 'Receipt No. 1',
            'notes' => array('notes_key_1' => 'Beam me up Scotty', 'notes_key_2' => 'Engage')
        ));
    }

    public function upiPayment()
    {
        return $this->api->paymentLink->create(array(
            'upi_link' => true,
            'amount' => 500,
            'currency' => 'INR',
            'expire_by' => now()->addMinutes(20)->timestamp,
            'reference_id' => 'TS1989',
            'description' => 'For XYZ purpose',
            'customer' => array('name' => 'Gaurav Kumar', 'email' => 'gaurav.kumar@example.com', 'contact' => '+919999999999'),
            'notify' => array('sms' => true, 'email' => true),
            'reminder_enable' => true,
            'notes' => array('policy_name' => 'Jeevan Bima')
        ));
    }
}
