<?php

namespace YallaTalk\Models;

use YallaTalk\Models\BaseModel as Model;
use YallaTalk\Models\User;
use YallaTalk\Models\Language;

class NativeLanguage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id', 'user_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     *
     */
    protected $table = 'native_languages';

    /**
     *
     * get user associate with the language
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get tha language info that the native language belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
