<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Iterinary extends Model {

    /**
     * touches the timestamps of related pivot table in iterinary_user pivot
     * @var array
     */
    
	/*
	*	this model has 3 types. planned, active and done
	*
	**/

	protected $table = 'iterinaries';

	/**
	 * Mass assignable model property
	 * 
	 * @var Array
	 */
	protected $fillable = ['route_id', 'pax','origin','destination'];
	

	/** 1 -- TO -- MANY
	 * This relationship defines the relationship of
	 * an iterinary an its original author/creator
	 * 
	 * @return type
	 */
	public function user()
	{
		return $this->belongsTo('App\User','creator_id');
	}

	/** MANY -- TO -- MANY
	 * This relationship defines the many to many
	 * relationship it has with different User entities
	 * 
	 * @return type
	 */
	public function users()
	{
		return $this->belongsToMany('App\User')->withPivot('date_start','status')->withTimestamps();;
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function days()
	{
		return $this->hasMany('App\Day');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
	public function transport()
	{
		return $this->hasOne('App\Transport');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function route()
	{
		return $this->belongsTo('App\Route');
	}

    /**
     * @param $query
     * @return mixed
     */
	public function scopePlanned($query)
	{
		return $query->where('status', '=', 'planned');
	}

    /**
     * @param $query
     * @return mixed
     */
	public function scopeDoing($query)
	{

        //return $query->wherePivot('pivotcolumn','=', $search);
		return $query->where('iterinary_user.status','=','doing');
	}

    /**
     * @param $query
     * @return mixed
     */
	public function scopeDone($query)
	{
		return $query->where('status', '=', 'done');
	}
	
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany('App\Activity');
    }

    /**
     * ratings relationship
     * @return dynamic relationship
     */
    public function ratings()
    {
        return $this->morphMany('App\Rating','ratingable');
    }

}
