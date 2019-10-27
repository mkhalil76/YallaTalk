<?php

namespace YallaTalk\Listeners;

use YallaTalk\Events\AccountStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdmin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AccountStatus  $event
     * @return void
     */
    public function handle(AccountStatus $event)
    {
        //
    }
}
