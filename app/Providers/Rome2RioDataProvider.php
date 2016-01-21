<?php

namespace App\Providers;


use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class Rome2RioDataProvider extends ServiceProvider {



	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		App::bind('Rome2RioData', function()
        {
            return new \Rome2RioData;
        });
	}

}
