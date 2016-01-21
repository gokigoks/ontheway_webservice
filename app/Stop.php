<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model {

	//
	protected $table = 'stops';

	public function segment()
	{
		return $this->belongsToMany('App\Segment');
	}
}
