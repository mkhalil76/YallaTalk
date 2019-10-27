<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\Language;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    /**
     * function to get all languges will be used in system
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getAll()
    {
        Cache::get('language', Language::all(), 10);
        $languages = Cache::get('language');
        return response()->json(['languages'=> $languages]);
    }
}
