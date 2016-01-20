<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {

	//
	public function eats()
	{
		return $this->query->where('type','=','eat');
	}

	public function hotels()
	{
		return $this->query->where('type','=','hotel');
	}

	public function spots()
	{
		return $this->query->where('type','=','spot');
	}
	
	public function user()
	{
		return $this->belongTo('App\User');
	}
	
}