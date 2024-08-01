<?php

namespace App\Http\Controllers;

use App\Mail\SampleMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use MailchimpTransactional\ApiClient;

class CaptchaController extends Controller
{
    public function index()
    {
        return view('captcha');
    }

    protected $client;
    protected $apiKey;


    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.mandrill.secret');
    }

    // public function sendEmail($to)
    // {
    //     // 

    //     $url = 'https://mandrillapp.com/api/1.0/messages/send.json';
    //     $data = [
    //         'key' => $this->apiKey,
    //         'message' => [
    //             'html' => '<p>sdsds</p>',
    //             'subject' => 'sdsdsdsd',
    //             'from_email' => 'gokulveuz@gmail.com',
    //             'from_name' => 'sdsdsdsdsd',
    //             'to' => [
    //                 [
    //                     'email' => $to,
    //                     'type' => 'to'
    //                 ]
    //             ]
    //         ]
    //     ];

    //     try {
    //         $response = $this->client->post($url, ['json' => $data]);
    //         return json_decode($response->getBody()->getContents(), true);
    //     } catch (RequestException $e) {
    //         dd($e);
    //         Log::error('Mandrill API request failed.', ['error' => $e->getMessage()]);
    //         return false;
    //     }
    // }


    public function post(Request $request)
    {


        $recaptcha = $request->input('g-recaptcha-response');

        $response = $this->verifyResponse($recaptcha);

        dd($response);
        if (isset($response['success']) and $response['success'] != true) {
            echo "An Error Occured and Error code is :" . $response['error-codes'];
        } else {
            echo "Correct Recaptcha";
        }


        try {

            $message = [
                "from_email" => "websoulgokulks@gmail.com",
                "subject" => "Hello world",
                "text" => "Welcome to Mailchimp Transactional!",
                "to" => [
                    [
                        "email" => "gokulveuz@gmail.com",
                        "type" => "to"
                    ]
                ]
            ];

            $mailchimp = new ApiClient();
            $mailchimp->setApiKey('md-Fch-T0w_pB-Y0yV0VAgE2g');
            $response = $mailchimp->messages->send(["message" => $message]);

            dd($response);
        } catch (Exception $e) {
            dd($e);
            echo 'Error: ', $e->getMessage(), "\n";
        }




        try {




            dd(343);
            // $client = new Client();

            // // Example request
            // $response = $client->post('https://api.example.com/send-mail', [
            //     'json' => [
            //         'to' => 'recipient@example.com',
            //         'subject' => 'Test Email',
            //         'body' => 'This is a test email.',
            //     ],
            // ]);


            // Mail::to('gokulveuz@gmail.com')->send(new SampleMail());
        } catch (Exception $e) {
            dd($e);
        }


        return redirect()->back();






        dd($recaptcha);
    }

    public function verifyResponse($recaptcha)
    {

        $remoteIp = $this->getIPAddress();

        // Discard empty solution submissions
        if (empty($recaptcha)) {
            return array(
                'success' => false,
                'error-codes' => 'missing-input',
            );
        }

        $getResponse = $this->getHTTP(
            array(
                'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
                'remoteip' => $remoteIp,
                'response' => $recaptcha,
            )
        );

        // get reCAPTCHA server response
        $responses = json_decode($getResponse, true);


        if (isset($responses['success']) and $responses['success'] == true) {
            $status = true;
        } else {
            $status = false;
            $error = (isset($responses['error-codes'])) ? $responses['error-codes'][0]
                : 'invalid-input-response';
        }

        return array(
            'success' => $status,
            'error-codes' => (isset($error)) ? $error : null,
        );
    }


    private function getIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    private function getHTTP($data)
    {

        $url = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query($data);
        $response = file_get_contents($url);

        return $response;
    }
}
