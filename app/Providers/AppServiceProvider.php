<?php

namespace YallaTalk\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // setup the limit for characters per migrations
        Schema::defaultStringLength(191);

        //load view composer with page info
        view()->composer('layouts.app', function ($view) {
            $view->with([
                'page' => 'YallaTalk Admin Panel'
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('mailgun.client', function () {
            return \Http\Adapter\Guzzle6\Client::createWithConfig([]);
        });
    }
}
