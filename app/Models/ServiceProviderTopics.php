<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\Topic;
use YallaTalk\Models\ServiceProvider;

class ServiceProviderTopics extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_provider_id',
        'topic_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'service_provider_topics';

    /**
     * get the topic for service provider topics
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * get the service provider for service provider topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(Topic::class);
    }
}
