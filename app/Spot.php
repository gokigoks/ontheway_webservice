<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model {

	public function activity(){
		return $this->belongsTo('App\Activity');
	}

}
