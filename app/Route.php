<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model {

	//
	protected $table = 'routes';


	/**
	 * Property models
	 * @var Array
	 * 
	 */
	
	protected $fillable = ['name' ,'distance', 'duration', 'price'];
	
	public function segments()
	{
		return $this->hasMany('App\Segment');
	}
	
	public function transport()
	{
		return $this->belongsTo('App\Transport');
	}

	public function iterinary()
	{
		return $this->belongsTo('App\Iterinary');
	}
}
