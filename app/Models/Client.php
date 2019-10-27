<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\User;
use YallaTalk\Models\Language;
use YallaTalk\Models\ClientLanguage;
use YallaTalk\Models\UserTransaction;
use Carbon\Carbon;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'clients';

    /**
     * Get the user that is client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get the languges the client want to learn
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'clients_languages');
    }

    /**
     *
     * get the client languges the for the client
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function clientLanguges()
    {
        return $this->hasMany(ClientLanguage::class);
    }

    /**
     * function to get user transaction associate with client
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function clientTransaction()
    {
        return $this->belongsToMany(UserTransaction::class, 'user_transactions');
    }

    /**
     * function to compute the client age
     *
     * @return  int
     */
    public function getClientAge()
    {
        if ($this->birth_of_date != null) {
            $birth_of_date = Carbon::parse($this->birth_of_date);
            $current_year = Carbon::now();
            return $current_year->diffInYears($birth_of_date);
        } else {
            return 0;
        }
    }
}
