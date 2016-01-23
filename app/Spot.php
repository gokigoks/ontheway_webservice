<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model {

	/**
	 * No not mistake with table 'stops'
	 * 
	 * @return String
	 */
	protected $table = 'spots';

	/**
	 * place_name - name of spot
	 * pos 		  - compressed long lat coordinates of spot	
	 * price 	  - price expended 
	 * tips       - tips for this activity
	 * 
	 * @return Array
	 */
	protected $fillable = ['place_name', 'pos', 'price', 'tips']

	public function activity(){
		$this->morphMany('App\Activity','typable');
	}

}
