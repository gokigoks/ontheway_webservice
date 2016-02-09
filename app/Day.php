<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model {

	/**
	 * Table used by Model
	 * 
	 * @var String
	 */
	protected $table = 'days';

	/**
	 * Fillable Attributes / Mass assignable attributes
	 * 
	 * @var Array
	 */
	protected $fillable = ['iterinary_id','day_no'];

	
	public function iterinary()
	{
		return $this->belongsTo('App\Iterinary');
	}

    public function segments()
    {
        return $this->hasMany('App\Segments');
    }

	public function activities()
	{
		return $this->hasMany('App\Activities');
	}

	
}
