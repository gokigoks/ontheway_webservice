<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class FlightIterinary extends Model {

	/**
	 * Database table for this model
	 * 
	 * @var String
	 */
	protected $table = 'flight_iterinaries';

	/**
	 * Model properties which can be mass assigned
	 * 
	 * @var Array
	 */
	protected $fillable = ['segment_id'];

	public function segment()
	{
		return $this->belongsTo('App\Segment');
	}
	
	public function hops()
	{
		return $this->hasMany('App\Hop');
	}


}
