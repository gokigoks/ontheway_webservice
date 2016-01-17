<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Iterinary extends Model {

	/*
	*	this model has 3 types. planned, active and done
	*
	**/

	protected $table = 'iterinaries';

	public function user()
	{
		return $this->belongsToMany('App\User');
	}

	
}
