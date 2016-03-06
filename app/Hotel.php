<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model {

	/**
	 * $table  = database table name
	 * @return type
	 */
	protected $table = 'hotels';
	
	//fillable data / mass assignable data
	protected $fillable = ['hotel_name', 'lng','lat', 'tips','pic_url','price,'];

	/**
	 * returns a polymorphic relationship with App\Activity
	 * 
	 * 
	 * @return type
	 */

	public function activity()
	{
		return $this->morphMany('App\Activity','typable');
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
