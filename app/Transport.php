<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model {

	/**
	 * DB Table for Transport Model
	 * 
	 * @var String
	 */
	protected $table = 'transports';

	

	public function routes()
	{	
		/**		
		 * check if route_id is set implying iterinary has route chosen already
		 * @return dynamic relationship
		 */
		if($this->attributes['route_id'] != null){
			return $this->hasOne('App\Route');
		}
		else{
			return $this->hasMany('App\Route');
		}

	}


	public function iterinary()
	{
		return $this->belongsTo('App\Iterinary');
	}


}
