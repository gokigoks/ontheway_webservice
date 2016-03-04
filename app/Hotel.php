<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model {

	/**
	 * $table  = database table name
	 * @return type
	 */
	protected $table = 'hotels';
	
	//fillable data / mass assignable data
	protected $fillable = ['hotel_name', 'pos', 'tips'];


	/**
	 * returns a polymorphic relationship with App\Activity
	 * 
	 * 
	 * @return type
	 */

	public function activity()
	{
		$this->morphMany('App\Activity','typable');
	}

}
