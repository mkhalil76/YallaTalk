<?php

namespace YallaTalk\Listeners;

use YallaTalk\Events\AccountStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use YallaTalk\Models\Appointment;

class FreezAccount
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
     * @return
     */
    public function handle(AccountStatus $event)
    {
        $provider = $event->provider;
        
        $appointments = Appointment::where('service_provider_id', '=', $provider->id)
            ->where('status', '=', Appointment::APPOINTMENT_REJECTED)
            ->count();
        
        if ($appointments%3 == 0) {
            $provider->account_status = 2;
            if ($provider->save()) {
                // return the response
                return [
                    'success' => false,
                ];
            }
        } else {
            return [
                'success' => true,
            ];
        }
    }
}
