<?php

namespace App\Providers;

use App\Mail\MailchimpTransport;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use MailchimpTransactional\ApiClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();

        // Mail::extend('mailchimp', function () {
        //     $client = new ApiClient();
        //     $client->setApiKey(config('services.mailchimp.api_key'));
            
        //     return new MailchimpTransport($client);
        // });

    }
}
