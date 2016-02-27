<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Activity
 *
 * @property integer $id
 * @property integer $day_id
 * @property string $start_time
 * @property string $end_time
 * @property string day
 * @property string $typable_type
 * @property integer $typable_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Activity $typable
 * @property-read \App\Stop $stop
 * @property integer $iterinary_id
 * @property-read \App\Iterinary $iterinary
 */
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
	

	
	/**s
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

    /*
     *  BelongTo Iterinary Model
     */
	public function iterinary(){
		return $this->belongsTo('App\Iterinary');
	}

    public function scopeCurrent($query)
    {
        return $query->where('end_time','=','')->orWhereNull('end_time');
    }

    public function scopeSpot($query)
    {
        return $query->where('typable_type','=','App\Spot');
    }

    public function scopeFood($query)
    {
        return $query->where('typable_type','=','App\Eat');
    }

    public function scopeTransport($query)
    {
        return $query->where('typable_type','=','App\Segment');
    }

    public function scopeUnfinished($query)
    {
        return $query->where('end_time','=','')->orWhereNull('end_time');
    }


}
