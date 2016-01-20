<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model {

	//
	public function segments()
	{
		return $this->hasMany('App\Segment');
	}
}
