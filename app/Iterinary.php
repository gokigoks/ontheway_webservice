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
	protected $fillable = ['route_id', 'pax','origin','destination'];
	

	/** 1 -- TO -- MANY
	 * This relationship defines the relationship of
	 * an iterinary an its original author/creator
	 * 
	 * @return type
	 */
	public function user()
	{
		return $this->belongTo('App\User','creator_id');
	}

	/** MANY -- TO -- MANY
	 * This relationship defines the many to many
	 * relationship it has with different User entities
	 * 
	 * @return type
	 */
	public function users()
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

	public function route()
	{
		return $this->hasOne('App\Route');
	}
	
	public function scopePlanned($query)
	{	

		return $query->where('status', '=', 'planned');
	} 

	public function scopeDoing($query)
	{
		return $query->where('status', '=', 'doing');
	}

	public function scopeDone($query)
	{
		return $query->where('status', '=', 'done');
	}
}
