<?php

namespace YallaTalk\Console\Commands;

use Illuminate\Console\Command;
use YallaTalk\Jobs\CheckNotJoindCalls;
use YallaTalk\Jobs\TrackCallsJob;
use YallaTalk\Jobs\OnlineUser;

class CallTrack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CallTrack:calltrack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command used to dispatch the call track and not joined jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //CheckNotJoindCalls::dispatch();
        //TrackCallsJob::dispatch();
        OnlineUser::dispatch();
    }
}
