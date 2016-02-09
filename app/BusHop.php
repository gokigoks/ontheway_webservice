<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BusHop
 *
 * @property integer $id
 * @property integer $segment_id
 * @property string $sName
 * @property string $tName
 * @property string $sPos
 * @property string $tPos
 * @property integer $frequency
 * @property integer $duration
 * @property integer $price
 * @property string $agency
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Segment $segment
 */
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
