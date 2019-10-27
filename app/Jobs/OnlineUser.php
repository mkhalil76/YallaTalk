<?php

namespace YallaTalk\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use YallaTalk\Models\ServiceProvider;

class OnlineUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $providers = ServiceProvider::all();
        $counter = 0;
        $array = [];
        foreach ($providers as $provider) {
            ++$counter;
            if (Cache::has('provider-is-online-' . $provider->id)) {
                $provider->availability = 1;
                $provider->save();
            } else {
                $provider->availability = 0;
                $provider->save();
            }
        }
    }
}
