<?php

namespace YallaTalk\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use YallaTalk\Models\Client;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\NativeLanguage;
use YallaTalk\Models\Invite;
use NotificationChannels\Apn\ApnChannel;
use NotificationChannels\Apn\ApnMessage;
use YallaTalk\Models\UserTransaction;
use YallaTalk\Models\Packege;
use YallaTalk\Models\UserPackege;
use YallaTalk\Models\Refund;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'users';
     
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     *
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject
     * claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added
     * to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * Get the client record associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Get the service provider record associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
     * get user native languages
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function nativeLanguage()
    {
        return $this->hasMany(NativeLanguage::class);
    }

    /**
     *function to return the invitations by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function invite()
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * function to generate apn token
     *
     * @return  string
     */
    public function routeNotificationForApn()
    {
        return $this->apn_token;
    }

    /**
     * function to get user transaction associate with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function userTransaction()
    {
        return $this->hasMany(UserTransaction::class);
    }

    /**
     * get the packege thats the user join
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function packege()
    {
        return $this->belongsToMany(Packege::class, 'user_packeges');
    }

    /**
     * get user packeges  for user
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function userPackeges()
    {
        return $this->hasMany(UserPackege::class);
    }

    /**
     * function to get the refunds for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function refund()
    {
        return $this->hasMany(UserPackege::class);
    }
}
