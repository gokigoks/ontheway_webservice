<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Interests extends Model {

	//
	protected $table = 'interests';

	public function user(){
		return $this->belongsToMany('App\User');
	}

}
