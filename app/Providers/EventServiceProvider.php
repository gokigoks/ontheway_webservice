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
        IterinaryWasCopied::class=>[
            UpdateContributionTable::class,
        ],
		IterinaryWasCreated::class=>[
			CreateContributionEntry::class,
		],
		IterinaryRateWasAdded::class=>[
			UpdateIterinaryWeighted::class,
		],
		ActivityRateWasAdded::class=>[
			UpdateActivityWeighted::class,
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
