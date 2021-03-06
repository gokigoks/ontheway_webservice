<?php namespace App\Providers;

use App\Events\IterinaryWasCopied;
use App\Events\IterinaryWasCreated;
use App\Events\IterinaryRateWasAdded;
use App\Events\ActivityRateWasAdded;

use App\Handlers\Events\UpdateContributionTable;
use App\Handlers\Events\CreateContributionEntry;
use App\Handlers\Events\UpdateIterinaryWeighted;
use App\Handlers\Events\UpdateActivityWeighted;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'event.name' => [
			'EventListener',
		],
        'App\Events\IterinaryWasCopied'=>[
            'App\Handlers\Events\UpdateContributionTable',
        ],
		'App\Events\IterinaryWasCreated'=>[
			'App\Handlers\Events\CreateContributionEntry',
		],
		'App\Events\IterinaryRateWasAdded'=>[
			'App\Handlers\Events\UpdateIterinaryWeighted',
		],
		'App\Events\ActivityRateWasAdded'=>[
			'App\Handlers\Events\UpdateActivityWeighted',
		],
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		//
	}

}
