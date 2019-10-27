<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\User;
use YallaTalk\Models\Topic;
use YallaTalk\Models\ServiceProviderRating;
use YallaTalk\Models\Appointment;
use YallaTalk\Models\Language;
use YallaTalk\Models\Call;
use Illuminate\Support\Facades\Cache;
use YallaTalk\Models\ServiceProviderLanguage;
use YallaTalk\Models\ServiceProviderTopics;
use YallaTalk\Models\UserTransaction;
use Carbon\Carbon;
use YallaTalk\Models\ProviderBank;

class ServiceProvider extends Model
{
    /**
     * Constant representing Online status.
     *
     * @var string
     */
    const ONLINE_STATUS = 1;

    /**
     * Constant representing offline status.
     *
     * @var string
     */
    const OFFLINE_STATUS = 0;

    /**
     * Constant representing video call type.
     *
     * @var string
     */
    const VIDEO_CALL_TYPE = 2;

    /**
     * Constant representing voice call type.
     *
     * @var string
     */
    const VOICE_CALL_TYPE = 1;

    /**
     * The table associated with the model.
     *
     * @var string.
     *
     */
    protected $table = 'service_providers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];

    /**
     * Get the user that is a service provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get the topics that the service provider belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function topic()
    {
        return $this->belongsToMany(Topic::class, 'service_provider_topics');
    }

    /**
     * get the rating associate with the service provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function rate()
    {
        return $this->hasMany(ServiceProviderRating::class);
    }

    /**
     * get the Appointments associate with the service provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * get the langueges thats the service provider want to learn
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function language()
    {
        return $this->belongsToMany(Language::class, 'service_provider_languages');
    }

    /**
     * get service provider calls
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    /**
     * get service provider langueges for Service Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function serviceProviderLanguage()
    {
        return $this->hasMany(ServiceProviderLanguage::class);
    }

    /**
     * get service provider topics for Service Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function serviceProviderTopic()
    {
        return $this->hasMany(ServiceProviderTopics::class);
    }

    /**
     * Scope a query to only include service provider by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query , $status
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('availability', '=', $status);
    }


    /**
     * Scope a query to only include service provider by gender.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query , $gender
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGender($query, $gender)
    {
        return $query->where('gender', '=', $gender);
    }
    /**
     * Scope a query to only include service provider by male.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMale($query, $status = null)
    {
        if ($status == null) {
            return $query->where('gender', '=', "M");
        }
    }
    
    /**
     * Scope a query to only include service provider by female.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFemale($query, $status = null)
    {
        if ($status == null) {
            return $query->where('gender', '=', "F");
        }
    }
    
    /**
     * Scope a query to only include service provider by voice call.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVoiceCall($query, $status = null)
    {
        if ($status == null) {
            return $query->where('call_type', '=', 1);
        }
    }
    
    /**
     * Scope a query to only include service provider by video call.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVideoCall($query, $status = null)
    {
        if ($status == null) {
            return $query->where('call_type', '=', 2);
        }
    }

    /**
     * Scope a query to only include service provider by call_type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query , $call_type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCallType($query, $call_type)
    {
        return $query->where('call_type', '=', $call_type);
    }

    /**
     * Scope a query to only include service provider by country.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query , $country
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCountry($query, $country)
    {
        return $query->join('users', function ($join) use ($country) {
            $join->on('users.id', '=', 'service_providers.user_id')
                ->where('users.country', '=', $country);
        });
    }

    /**
     * Scope a query to only include service provider by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query , $name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProviderName($query, $name)
    {
        return $query->join('users', function ($join) use ($name) {
            $join->on('users.id', '=', 'service_providers.user_id')
                ->where('users.first_name', 'like', '%'.$name.'%');
        });
    }

    /**
     * Scope a query to only include service provider by topic.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query , $topic_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopic($query, $topic_id)
    {
        return $query->join('service_provider_topics', function ($join) use ($topic_id) {
            $join->on('service_provider_topics.service_provider_id', '=', 'service_providers.id')
                ->where('service_provider_topics.topic_id', '=', $topic_id);
        });
    }

    /**
     * function to get user transaction associate with service provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function clientTransaction()
    {
        return $this->belongsToMany(UserTransaction::class, 'user_transactions');
    }
    
    /**
     * function to check if the service provider is onlline or not
     *
     * @return   boolean
     */
    public function isOnline()
    {
        if (Cache::has('provider-is-online-' . $this->id)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * function to get the provider age
     *
     * @return  int
     */
    public function getProviderAge()
    {
        if ($this->birth_of_date != null) {
            $birth_of_date = Carbon::parse($this->birth_of_date);
            $current_year = Carbon::now();
            return $current_year->diffInYears($birth_of_date);
        } else {
            return 0;
        }
    }

    /**
     * Get the bank account informations that is belong to the Service provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function bankAccount()
    {
        return $this->hasOne(ProviderBank::class);
    }
}
