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
}
