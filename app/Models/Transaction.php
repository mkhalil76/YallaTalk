<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use Yallatalk\Models\User;
use Yallatalk\Models\UserTransaction;
use Yalltalk\Models\Client;
use Yalltalk\Models\ServiceProvider;

class Transaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'transactions';

    /**
     * function to get client assoicate with transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function client()
    {
        return $this->belongsToMany(Client::class, 'user_transactions');
    }

    /**
     * function to get client assoicate with transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function serviceProvider()
    {
        return $this->belongsToMany(ServiceProvider::class, 'user_transactions');
    }
    
    /**
     * function to get user transaction associate with transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function userTransaction()
    {
        return $this->hasMany(UserTransaction::class);
    }
}
