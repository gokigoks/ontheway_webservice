<?php namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class IterinaryWasCopied extends Event
{
    use SerializesModels;
    public $iterinary_id;

    /**
     * Create a new event instance.
     * @param iterinary_id
     */
    public function __construct($iterinary_id)
    {
        $this->iterinary_id = $iterinary_id;
    }
}