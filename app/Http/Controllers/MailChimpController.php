<?php

namespace App\Http\Controllers;

use App\Mail\SampleMail;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MailchimpTransactional\ApiClient;
use Paytabscom\Laravel_paytabs\Facades\paypage;

class MailChimpController extends Controller
{
    public $apiKey;
    public $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.mandrill.secret');
    }

    public function index()
    {
        return view('mail');
    }

    public function post(Request $request)
    {

        Mail::raw('This is a test email.', function ($message) {
            $message->to('gokulveuz@gmail.com')
                ->subject('Test Email via Ngrok');
        });

        return redirect()->back()->with('success', 'Done');


        // $pay = paypage::sendPaymentCode($payment_method)
        //     ->sendTransaction($tran_type,$tran_class)
        //     ->sendCart($cart_id, $cart_amount, $cart_description)
        //     ->sendCustomerDetails($name, $email, $phone, $street1, $city, $state, $country, $zip, $ip)
        //     ->sendShippingDetails($same_as_billing, $name = null, $email = null, $phone = null, $street1= null, $city = null, $state = null, $country = null, $zip = null, $ip = null)
        //     ->sendHideShipping($on = false)
        //     ->sendURLs($return, $callback)
        //     ->sendLanguage($language)
        //     ->sendFramed($on = false)
        //     ->sendTokinse($status)
        //     ->sendToken($tran_ref,$token)
        //     ->create_pay_page(); // to initiate payment page


        // $pay = paypage::sendPaymentCode('all')
        //     ->sendTransaction('sale','ecom')
        //     ->sendCart(10,1000,'test')
        //     ->sendCustomerDetails('Walaa Elsaeed', 'w.elsaeed@paytabs.com', '0101111111', 'test', 'Nasr City', 'Cairo', 'EG', '1234','100.279.20.10')
        //     ->sendShippingDetails('Walaa Elsaeed', 'w.elsaeed@paytabs.com', '0101111111', 'test', 'Nasr City', 'Cairo', 'EG', '1234','100.279.20.10')
        //     ->sendURLs('http://127.0.0.1:8001/callback')
        //     ->sendLanguage('en')
        //     ->create_pay_page();


        //     dd($pay);

        // if ($response->successful()) {
        //     return redirect($result['redirect_url']);
        // } else {
        //     return back()->withErrors(['msg' => $result['message'] ?? 'Payment request failed.']);
        // }


        // $pay = paypage::sendPaymentCode('all')
        //     ->sendTransaction('sale', 'ecom')
        //     ->sendCart(10, 1000, 'test')
        //     ->sendCustomerDetails('Walaa Elsaeed', 'w.elsaeed@paytabs.com', '0101111111', 'test', 'Nasr City', 'Cairo', 'EG', '1234', '100.279.20.10')
        //     ->sendShippingDetails('Walaa Elsaeed', 'w.elsaeed@paytabs.com', '0101111111', 'test', 'Nasr City', 'Cairo', 'EG', '1234', '100.279.20.10')
        //     ->sendURLs('http://127.0.0.1:8000/callback', 'http://127.0.0.1:8000/callback')
        //     ->sendLanguage('en')
        //     ->create_pay_page();

        //     dd($pay);
        // return $pay;




        // $request->validate([
        //     'name' => 'required',
        //     'subject' => 'required',
        //     'message' => 'required'
        // ]);


        // Mail::raw('This is a test email', function ($message) {
        //     $message->to('websoulgokulks@gmail.com')
        //         ->subject('Test Email');
        // });

        // Mail::send('welcome', [], function ($message){
        //     $message->to('websoulgokulks@gmail.com')->subject('Testing mail');
        // });

        // Mail::to('websoulgokulks@gmail.com')->send(new SampleMail($request->all()));

        $data =  $this->sendEmail('websoulgokulks@gmail.com');


        dd($data);

        return redirect()->back()->with('success', 'Done');
    }


    public function sendEmail($to)
    {

        try {


            $mailchimp = new ApiClient();
            $mailchimp->setApiKey($this->apiKey);

            // $response = $mailchimp->senders->addDomain(["domain" => "localhost:8000"]);


            $response = $mailchimp->messages->send(["message" => [
                'html' => '<p>sdsaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaads</p>',
                'subject' => 'test mail from web',
                'from_email' => 'gokulveuz@gmail.com',
                'from_name' => 'Gokul',
                'to' => [
                    [
                        'email' => $to,
                        'type' => 'to'
                    ]
                ],
                'metadata' => array('website' => '127.0.0.1'),
            ]]);

            // dd($response);


            // $url = 'https://mandrillapp.com/api/1.0/messages/send.json';
            // $data = [
            //     'key' => $this->apiKey,
            //     'message' => [
            //         'html' => '<p>sdsaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaads</p>',
            //         'subject' => 'test mail from web',
            //         'from_email' => 'gokulveuz@gmail.com',
            //         'from_name' => 'Gokul',
            //         'to' => [
            //             [
            //                 'email' => $to,
            //                 'type' => 'to'
            //             ]
            //         ]
            //     ],

            // ];


            // $response = $this->client->post($url, ['json' => $data]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            dd($e);
            Log::error('Mandrill API request failed.', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function callback()
    {
        dd(request()->all());
        $callbackData = file_get_contents('php://input');
        $data = json_decode($callbackData, true);


        dd($data);
    }
}
