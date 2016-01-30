<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class BusHop extends Model {

	/**
	 * Table Model is using
	 * 
	 * @var String
	 */
	protected $table = 'bus_hops';

	/**
	 * Model properties
	 * segment_id 		= foreign key to segments table
	 * sName 			= origin name
	 * tName 			= destination name
	 * sPos 			= origin long lat
	 * tPos 			= destination long lat
	 * frequency 		= frequency of trips
	 * price 			= price for trip
	 * duration 		= duration of trip
	 * agency	 		= agency of Bus
	 * 
	 * @var Array
	 */
	protected $fillable = ['segment_id','sName','tName','sPos','tPos','frequency','duration','price'];

	public function segment()
	{

		return $this->belongsTo('App\Segment');

	}
	


}
