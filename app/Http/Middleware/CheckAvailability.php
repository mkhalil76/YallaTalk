<?php

namespace YallaTalk\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;
use UserHelper;
use Illuminate\Support\Facades\Cache;

class CheckAvailability
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if(Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(3);
            $user_id = Auth::user()->id;
            $service_provider_id = UserHelper::getServiceProviderID($user_id);
            
            Cache::put('provider-is-online-' . $service_provider_id, true, $expiresAt);
        }
        return $next($request);
    }
}
