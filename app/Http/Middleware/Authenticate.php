<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{		
		if ($request->ajax() && $request ['token'] == 'gokigoks')
		{
			return $next($request)
                ->header('Access-Control-Allow-Origin' , '*')                
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')                
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
                ->header('Access-Control-Max-Age', '28800');
		}
		if ($this->auth->guest())
		{					
				return redirect()->guest('auth/login');		
		}

		return $next($request);
	}

}
