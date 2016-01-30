<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model {

	//
	protected $table = 'segments';

	/**
	 * Data
	 * route_id
	 * sequence             - sequence of segment
	 * origin_name          - origin name
	 * origin_pos           - compressed long lat of origin
	 * destination_name     - name of the destination place
	 * destination_pos		- compressed long lat of destination
	 * price 				- price for segment
	 * distance             - distance between origin and destination
	 * duration 			- duration for the travel segment
	 * mode 				- mode of transportation for this segment
	 * @var Array
	 */
	protected $fillable = ['route_id','sequence','origin_name','origin_pos','destination_name', 'destination_pos','price','distance','duration','mode'];


	public function stops()
	{
		return $this->hasMany('App\Stop');
	}

	public function route(){
		return $this->belongsTo('App\Route');
	}

	public function flight_iterinaries()
	{
		if($this->attributes['mode'] == "flight")
		{
			return $this->hasMany('App\FlightIterinary');
		}
		else
		{
			return "no flights";
		}
	}

	/**
	 * this model has different associated models depending on 
	 * mode property of current object
	 * 
	 * @return Related Model
	 */
	public function hops(){
		/**
		 *	Access the current model's attributes and check the mode
		 *  for the current Segment and returns an appropriate relationship
		 * 
		 * 
		 * @param   $paramname description
		 */
		if($this->attributes['mode'] == 'bus'){

			return $this->hasMany('App\BusHop');
		}
		if($this->attributes['mode'] == 'ferry')
		{
			return $this->hasMany('App\FerryHop');
		}
		if($this->attributes['mode'] == 'train');
		{
			return $this->hasMany('App\TrainHop');
		}

	}
}
