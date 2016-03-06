<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Eat
 * @property-write String $place_name
 * @property-write String $lng
 * @property-write String $lat
 * @property-write String tips
 * @property-write String price
 * @property-write String main_category_id
 * @property-write String sub_category_id
 * @property-write String pic_url
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
	protected $fillable = ['place_name', 'price', 'tips', 'lng','lat','pic_url'];

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
