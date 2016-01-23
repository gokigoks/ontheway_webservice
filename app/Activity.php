<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	//
	/**
	 * day to activity
	 * 1 to many
	 * @return type
	 */
	public function day()
	{
		return $this->belongsTo('App\Day');
	}

	public function typable()
	{
		return $this->morphTo();
	}

}
