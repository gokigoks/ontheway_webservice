<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {

	/**
	 * Table used by this model
	 * 
	 * @var String
	 */
	protected $table = 'ratings';

	protected $fillable = ['user_id', 'value', 'ratingable_id', 'ratingable_type'];
	//
	public function ratingable()
	{
		return $this->morphTo();
	}
	
	public function user()
	{
		return $this->belongTo('App\User');
	}
	
}