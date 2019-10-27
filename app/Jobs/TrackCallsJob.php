<?php

namespace YallaTalk\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use YallaTalk\Models\Call;
use YallaTalk\Models\Appointment;
use Carbon\Carbon;
use PushNotification;
use UserHelper;

class TrackCallsJob implements ShouldQueue
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
        $calls = Call::where('room_status', '=', Call::ROOM_JOIN_STATUS)->get();
        if (count($calls) > 0) {
            foreach ($calls as $call) {
                $client_info = Client::findOrFail($call->client_id);
                $client_time_zone = UserHelper::getTimeZone($client_info->user_id);
                $current_time = Carbon::now($client_time_zone);
                $call_end_time = $call->end_at;
                $call_end_time = Carbon::parse($call_end_time);
                if ($call_end_time == $current_time && UserHelper::hasNextCall($call->service_provider_id, $call->id)) {
                }
                if (UserHelper::callBalance($call) == 0) {
                }
            }
        }
    }
}
