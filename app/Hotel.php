<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model {

	//
	protected $table = 'hotels';
	/**
	 * $table  = database table name
	 * @return type
	 */

	public function activity()
	{
		$this->morphMany('App\Activity','typable');
	}

			

}
