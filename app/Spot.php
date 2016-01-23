<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model {

	public function activity(){
		$this->morphMany('App\Activity','typable');
	}

}
