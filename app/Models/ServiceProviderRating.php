<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\ServiceProvider;

class ServiceProviderRating extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'service_provider_ratings';
    
    /**
     *
     * get the service provider the rating belngs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
