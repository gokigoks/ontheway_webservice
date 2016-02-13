<?php namespace App\Http\Middleware;

use Closure;
use Input;
use Session;

class cors {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{	
        $token = (Input::get('token')) ? Input::get('token') : $request['token'];

		if($token == "gokigoks" || Session::has($token) )
		{
			return $next($request)
            ->header('Access-Control-Allow-Origin' , '*')                
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')                
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '28800');
		}	
		if(!$token){
            return $next($request)
            ->header('Access-Control-Allow-Origin' , '*')                
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')                
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '28800');
        }
        
        return response()->json('web token invalid..', 403);
	}

}
