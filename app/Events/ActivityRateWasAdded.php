<?php namespace App\Events;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class ActivityRateWasAdded extends Event
{
    use SerializesModels;
    public $activity_id;

    /**
     * Create a new event instance.
     * @param $id
     */
    public function __construct($id)
    {
        $this->activity_id = $id;
    }
}