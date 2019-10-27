<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\Language;
use YallaTalk\Models\ServiceProvider;

class ServiceProviderLanguage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_provider_id','language_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'service_provider_languages';

    /**
     * get the languages for service provider language
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function language()
    {
        return $this->belongsTo(language::class);
    }

    /**
     * get the service provider for service provider language
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
