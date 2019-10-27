<?php

namespace YallaTalk\Providers;

use Illuminate\Support\ServiceProvider;
use App;

class YallaTalkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('userhelper', function () {
            return new \YallaTalk\Http\Helpers\UserHelper;
        });
    }
}
