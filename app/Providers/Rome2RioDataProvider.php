<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class Rome2RioDataProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		App::bind('Rome2RioData', function()
        {
            return new \App\Classes\Rome2RioData;
        });
	}

}
