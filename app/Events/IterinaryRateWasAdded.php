<?php namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class IterinaryRateWasAdded extends Event
{

    use SerializesModels;
    public $iterinary_id;

    /**
     * Create a new event instance.
     * @param $id
     */
    public function __construct($id)
    {
        $this->iterinary_id = $id;
    }
}