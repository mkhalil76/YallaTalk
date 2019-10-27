<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use Yallatalk\Models\User;
use Yallatalk\Models\Transaction;
use Yallatalk\Models\Client;
use Yallatalk\Models\ServiceProvider;

class UserTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'user_transactions';

    /**
     * function to get client transactions associate with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * function to get client transactions associate with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
    /**
     * function to get user transaction assoicate with transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
