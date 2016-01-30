<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Interests extends Model {

	/**
	 * Table associated with interests
	 * 
	 * @var String
	 */
	protected $table = 'interests';

	protected $fillable = ['interest_name'];


	
	public function user(){
		return $this->belongsToMany('App\User');
	}

	

}
