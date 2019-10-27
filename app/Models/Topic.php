<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\ServiceProviderTopics;

class Topic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'topics';

    /**
     * get the service providers for the Topic
     *
     * specify the pivot table name
     * (pass the service_provider_topics table as second parameter
     * for the relation method)
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function serviceProviders()
    {
        return $this->belongsToMany(ServiceProvider::class, 'service_provider_topics');
    }

    /**
     * get the service provider topics for topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function serviceProviderTopics()
    {
        return $this->hasMany(Topic::class);
    }
}
