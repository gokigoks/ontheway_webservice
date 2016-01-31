<?php namespace App\Providers;


use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class GeolocationDataProvider extends ServiceProvider {

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		App::bind('Geolocation', function()
        {
            return new \GeolocationHelper;
        });
	}

}
