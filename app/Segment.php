<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model {

	//
	protected $table = 'segments';

	public function stops()
	{
		return $this->hasMany('App\Stop');
	}

	public function route(){
		return $this->belongsTo('App\Route');
	}
}
