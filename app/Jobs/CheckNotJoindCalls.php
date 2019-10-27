<?php

namespace YallaTalk\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use YallaTalk\Models\Call;
use Carbon\Carbon;
use PushNotification;
use UserHelper;
use YallaTalk\Models\Client;

class CheckNotJoindCalls implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $calls = Call::where('room_status', '=', Call::ROOM_WAITING_STATUS)->get();
        if (count($calls) > 0) {
            foreach ($calls as $call) {
                $client_info = Client::findOrFail($call->client_id);
                $client_time_zone = UserHelper::getTimeZone($client_info->user_id);
                $current_time = Carbon::now($client_time_zone);
                $call_start_time = $call->start_at;
                $call_start_time = Carbon::parse($call_start_time);
                if ($call_start_time->addMinutes(2) < $current_time) {
                    UserHelper::endCall($call->id);
                }
            }
        }
    }
}
