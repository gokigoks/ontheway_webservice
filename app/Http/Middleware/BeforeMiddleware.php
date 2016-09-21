<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use Response;
class BeforeMiddleware
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
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');

        if (Request::getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            

           return $next($request)
            ->header('Access-Control-Allow-Origin' , '*')            
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')            
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '28800');
        }

        return $next($request)
            ->header('Access-Control-Allow-Origin' , '*')        
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')            
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '28800');
    }
}
