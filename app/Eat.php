<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Eat extends Model {

	protected $table = 'eats';


	public function activity(){
		return $this->morphMany('App\Activity','typable');
	}
	
	public function ratings()
	{
		return $this->morphMany('App\Rating','ratingable')
	}

}
