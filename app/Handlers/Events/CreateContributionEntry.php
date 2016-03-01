<?php namespace App\Handlers\Events;

use App\Events\IterinaryWasCreated;
use App\Contribution;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class CreateContributionEntry implements ShouldBeQueued
{
    use InteractsWithQueue;

    /**
     * Create the event handler.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  IterinaryWasCreated $event
     * @return void
     */
    public function handle(IterinaryWasCreated $event)
    {
        //creates an entry to contribution table if iterinary is made by the user
        $contribution = new Contribution;
        $contribution->user_id = $event->user_id;
        $contribution->iterinary_id = $event->iterinary_id;
        $contribution->points = 0;
        $contribution->save();
    }
}