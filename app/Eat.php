<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Eat
 * @property-write String $place_name
 * @property-write String $pos
 * @package App
 */
class Eat extends Model {

	/**
	 * Table used by model
	 * 
	 * @var String
	 */
	protected $table = 'eats';

	/**
	 * Mass assignable model data
	 * @var Array
	 */
	protected $fillable = ['place_name', 'price', 'tips', 'pos','pic_url'];

	/**
	 * activity relationship
	 * @return dynamic relationship
	 */

	public function activity(){
		return $this->morphMany('App\Activity','typable');
	}
	
	/**
	 * ratings relationship
	 * @return dynamic relationship
	 */
	public function ratings()
	{
		return $this->morphMany('App\Rating','ratingable');
	}

	public function category()
	{
		return $this->belongsToMany('App\FoodCategory');
	}

}
