<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Iterinary extends Model {

	/*
	*	this model has 3 types. planned, active and done
	*
	**/

	protected $table = 'iterinaries';

	/**
	 * Mass assignable model property
	 * 
	 * @var Array
	 */
	protected $fillable = ['transport_id', 'pax','origin','destination'];


	//
	public function user()
	{
		return $this->belongsToMany('App\User');
	}

	public function days()
	{
		return $this->hasMany('App\Day');
	}
	
	public function transport()
	{
		return $this->hasOne('App\Transport');
	}
	
}
