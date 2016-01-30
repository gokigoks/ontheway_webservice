<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Hop extends Model {

	/**
	 * Table for Hops model
	 * @var String
	 */
	protected $table = 'hops';

	/**
	 * 					--VARIABLES--
	 * flight_iterinary_id 		- foreign key to the flight iterinary
	 * source_area_code			- area code of origin
	 * target_area_code			- area code of destination
	 * source_terminal			- terminal of origin
	 * target_terminal			- terminal of target
	 * sTime 					- time of departure
	 * tTime 					- time of arrival
	 * flight_no				- flight number
	 * airline_code 			- code of airline
	 * duration 				- flight duration
	 * 
	 * @var Array
	 */
	protected $fillable = ['segment_id','source_area_code','target_area_code','source_terminal','target_terminal','sTime','tTime','flight_no','airline_code','duration'];
 
	public function segment()
	{
		return $this->belongsTo('App\Segment');
	}

	//end of line
}
