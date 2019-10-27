<?php

namespace YallaTalk;

use Illuminate\Database\Eloquent\Model;
use YallaTalk\Models\Packege;
use YallaTalk\Models\User;

class UserPackege extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'user_packeges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','packege_id'
    ];

    /**
     * get the packages for user packege
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function package()
    {
        return $this->belongsTo(Packege::class);
    }

    /**
     * function to get the users for the user packeges
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
