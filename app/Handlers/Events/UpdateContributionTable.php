<?php namespace App\Handlers\Events;

use App\Events\IterinaryWasCopied;
use App\Contribution;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;


class UpdateContributionTable implements ShouldBeQueued
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
     * @param  IterinaryWasCopied $event
     * @return void
     */
    public function handle(IterinaryWasCopied $event)
    {
        $contribution = Contribution::where('iterinary_id', '=', $event->iterinary_id)->get();
        $contribution->points += 5;
        $contribution->save();
    }
}