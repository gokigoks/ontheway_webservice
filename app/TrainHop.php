<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainHop extends Model {

	
	/**
	 * Table Model is using
	 * 
	 * @var String
	 */
	protected $table = 'train_hops';

	/**
	 * Model properties
	 * segment_id 		= foreign key to segments table
	 * sName 			= origin name
	 * tName 			= destination name
	 * sPos 			= origin long lat
	 * tPos 			= destination long lat
	 * frequency 		= frequency of trips
	 * duration 		= duration of trip
	 * agency	 		= agency of train
	 * 
	 * @var Array
	 */
	protected $fillable = ['segment_id','url', 'sName','tName','sPos','tPos','frequency','duration','agency'];

	public function segment()
	{

		return $this->belongsTo('App\Segment');

	}
	
}	
