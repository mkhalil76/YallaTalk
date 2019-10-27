<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;

class Invite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'token'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'invites';
}
