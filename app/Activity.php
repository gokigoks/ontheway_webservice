<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	//

	public function day()
	{
		return $this->BelongsTo('App\Day');
	}

}
