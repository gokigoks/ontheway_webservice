<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model {

	/**
	 * Table for Stops Model
	 * @var String
	 */
	protected $table = 'stops';

	/**
	 * name 			- name of STOP
	 * kind 			- kind of stop E.G.(bus station, trainstation, INN)	
	 * city 			- price expended 
	 * pos 				- compressed long lat coordinates of STOP
	 * tips 			- tips for this activity
	 * timezone 		- timezone in the STOP locale
	 * region_code 		- region_code of the STOP area
	 *  
	 * @var Array
	 */
	protected $fillable = ['name', 'kind', 'city', 'pos', 'tips', 'timezone', 'region_code'];

	public function segment()
	{
		return $this->belongsToMany('App\Segment');
	}
	
	public function activities()
	{
		return $this->belongsToMany('App\Activity');
	}
}
