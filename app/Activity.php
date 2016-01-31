<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	/**
	 * table used by model
	 * @var String
	 */
	protected $table = 'activities';

	/**
	 * 
	 * fillable attributes of the model
	 * @var Array
	 * 
	**/

	protected $fillable = ['day_id','start_time','end_time','typable_type','typable_id'];
	
	/**
	 * day to activity
	 * 1 to many
	 * @return relationship
	 */

	public function day()
	{
		return $this->belongsTo('App\Day');
	}

	/**
	 * Define Morphing Relationship
	 * @param uses typable_type and typable_id
	 * @return Morphed Relationship
	 */
	public function typable()
	{
		return $this->morphTo();
	}

	public function stop(){
		return $this->belongsTo('App\Stop');
	}	
}
