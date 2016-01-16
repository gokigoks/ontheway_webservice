<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model {

	public function iterinary()
	{
		return $this->belongsTo('App\Iterinary');
	}

}
