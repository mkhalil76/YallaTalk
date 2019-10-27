<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\User;
use YallaTalk\Models\UserPackege;

class Packege extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'packeges';

    /**
     * get the users thats join the package
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_packeges');
    }

    /**
     * get user packeges  for packege
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function userPackeges()
    {
        return $this->hasMany(UserPackege::class);
    }
}
