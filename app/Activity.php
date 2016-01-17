<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	//

	public function day()
	{
		return $this->BelongsTo('App\Day');
	}

	public function eats()
	{
		return $this->hasMany('App\Eats');
	}

}
