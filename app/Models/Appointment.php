<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\Client;

class Appointment extends Model
{
    /**
     * Constant representing Appoitments pending.
     *
     * @var string
     */
    const APPOINTMENT_PENDING = "PENDING";

    /**
     * Constant representing Appoitments approved.
     *
     * @var string
     */
    const APPOINTMENT_APPROVED = "APPROVED";

    /**
     * Constant representing Appoitments rejected.
     *
     * @var string
     */
    const APPOINTMENT_REJECTED = "REJECTED";
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    
    protected $table = 'appoitments';

    /**
     * get service provider belong to Appoitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    /**
     * get client belong to Appoitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
