<?php namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class IterinaryWasCreated extends Event
{
    use SerializesModels;
    public $user_id;
    public $iterinary_id;

    /**
     * Create a new event instance.
     * @param $user_id
     * @param $iterinary_id
     */
    public function __construct($user_id, $iterinary_id)
    {
        $this->user_id = $user_id;
        $this->iterinary_id = $iterinary_id;
    }
}