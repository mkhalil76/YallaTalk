<?php

namespace YallaTalk\Models;

use Illuminate\Database\Eloquent\Model;
use YallaTalk\Models\ServiceProvider;

class ProviderBank extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string.
     *
     */
    protected $table = 'provider_banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_provider_id'
    ];

    /**
     * Get the service provider that is a belong to the bacnk account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
