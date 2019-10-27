<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\Client;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\ServiceProviderRating;
use YallaTalk\Models\ClientLanguage;
use YallaTalk\Models\ServiceProviderLanguage;

class Language extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'languages';

    /**
     * get the clients want to learn the language
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clients_languages');
    }

    /**
     * get service provider how learn langusges
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function serviceProvider()
    {
        return $this->belongsToMany(ServiceProvider::class, 'service_provider_languages');
    }

    /**
     * get all language ration
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function rate()
    {
        return $this->hasMany(ServiceProviderRating::class);
    }

    /**
     *
     * get the language  for the client langguage
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function clientLanguges()
    {
        return $this->hasMany(ClientLanguage::class);
    }

    /**
     * get the service provider languages or the Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function serviceProviderLanguage()
    {
        return $this->hasMany(ServiceProviderLanguage::class);
    }
}
