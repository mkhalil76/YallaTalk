<?php

namespace YallaTalk\Http\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\Call;
use Carbon\Carbon;

class CallsController extends Controller
{
    public function getCurrentCalls()
    {
        $current_time = Carbon::now();
        $current_time = date("Y-m-d H:i:s", strtotime($current_time));
        $current_calls = Call::all();

        return $current_calls;
    }
}
