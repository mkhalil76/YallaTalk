<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use Carbon\Carbon;
use YallaTalk\Models\ServiceProvider;

class Call extends Model
{

    /**
     * Constant representing Room completed status.
     *
     * @var string
     */
    const ROOM_COMPLETED_STATUS = "COMPLETED";

    /**
     * Constant representing Room Join status.
     *
     * @var string
     */
    const ROOM_JOIN_STATUS = "JOIN";

    /**
     * Constant representing Room waittng status.
     *
     * @var string
     */
    const ROOM_WAITING_STATUS = "WAITING";

    /**
     * Constant representing Room waittng status.
     *
     * @var string
     */
    const ROOM_REJECTED_STATUS = "REJECTED";

    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'calls';

    /**
     * Get the service provider calls.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    /**
     * function to compute the call duration
     *
     * @return time
     */
    public function getCallDuration()
    {
        if ($this->start_at == null || $this->end_at == null) {
            return 0;
        }
        $start = Carbon::parse($this->start_at);
        $end = Carbon::parse($this->end_at);
        return $end->diffInMinutes($start);
    }

    /**
     * function to compute seconds duration
     *
     * @return  time
     */
    public function getSecondsDuration()
    {
        if ($this->start_at == null || $this->end_at == null) {
            return 0;
        }
        $start = Carbon::parse($this->start_at);
        $end = Carbon::parse($this->end_at);
        return $end->diffInSeconds($start)%60;
    }
}
