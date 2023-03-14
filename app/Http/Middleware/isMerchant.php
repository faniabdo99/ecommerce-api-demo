<?php

namespace App\Http\Middleware;

use Closure;

class isMerchant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if(auth()->check()){
            if(auth()->user()->type != 'merchant'){
                response()->json(['message' => 'You have to be a merchant to do this!'], 403)->send();
                die;
            }
        }else{
            response()->json(['message' => 'You have to be a merchant to do this!'], 403)->send();
            die;
        }
        return $next($request);
    }
}
