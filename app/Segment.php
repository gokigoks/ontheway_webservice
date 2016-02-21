<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model {

	//
	protected $table = 'segments';

	/**
	 * @property Ingteger route_id
	 * @property Integer sequence             - sequence of segment
	 * @property String origin_name          - origin name
	 * @property String origin_pos           - compressed long lat of origin
	 * @property String destination_name     - name of the destination place
	 * @property String destination_pos		- compressed long lat of destination
	 * @property String sCode                - Area code of origin (used in flights segment)
	 * @property String tCode 				- Area code of destination (used in flights segment)
	 * @property Integer price 				- price for segment
	 * @property Integer distance             - distance between origin and destination
	 * @property Integer duration 			- duration for the travel segment
	 * @property Sring mode 				- mode of transportation for this segment
	 * @var Array
	 */
	protected $fillable = ['route_id','sequence','origin_name','origin_pos','destination_name', 'sCode', 'tCode', 'destination_pos','price','distance','duration','mode'];


	public function stops()
	{
		return $this->hasMany('App\Stop');
	}

    /**
     * @param $query
     * @param $day
     * @return mixed
     */
    public function scopeDay($query,$day)
    {
        return $query->where('day','=',$day);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function route(){
		return $this->belongsTo('App\Route');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|string
     */
	public function flight_iterinaries()
	{
		if($this->attributes['mode'] == "flight")
		{
			return $this->hasMany('App\FlightIterinary');
		}
		else
		{
			return "no flights";
		}
	}

	/**
	 * this model has different associated models depending on 
	 * mode property of current object
	 * 
	 * @return Related Model
	 */
	public function hops(){
		/**
		 *	Access the current model's attributes and check the mode
		 *  for the current Segment and returns an appropriate relationship
		 * 
		 * 
		 * @param   $paramname description
		 */
		if($this->attributes['mode'] == 'bus')
		{
			return $this->hasMany('App\BusHop');
		}
		if($this->attributes['mode'] == 'ferry')
		{
			return $this->hasMany('App\FerryHop');
		}
		if($this->attributes['mode'] == 'train');
		{
			return $this->hasMany('App\TrainHop');
		}

	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
	public function activity()
	{

        return $this->morphMany('App\Activity','typable');
	}
}
