<?php

namespace YallaTalk\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * function to show settings main page
     *
     * @return  view
     */
    public function index()
    {
        $commision = Cache::get('commision');
        return view('settings.index', compact('commision'));
    }

    /**
     * function to update the commision value
     *
     * @param  Request $request
     *
     * @return  view
     */
    public function updateCommision(Request $request)
    {
        $value = $request->amount;
        Cache::forever('commision', $value);

        return redirect('admin/settings/');
    }
}
