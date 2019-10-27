<?php

namespace YallaTalk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     *
     */
    protected $dates = ['deleted_at'];
    
    /**
     * protected database connection variable
     *
     *
     */
    protected $connection;

    /** Create a new Eloquent model instance.
     *
     * set default database connection to mongodb connection
     *
     * @param  array  $attributes
     *
     * @return void
     *
     */
    public function __construct(array $attributes = [])
    {
        $this->connection = 'mysql';
        parent::__construct($attributes);
    }
}
