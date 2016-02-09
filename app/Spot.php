<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model {

	/**
	 * No not mistake with table 'stops'
	 * 
	 * @return String
	 */
	protected $table = 'spots';

	/**
	 * place_name - name of spot
	 * lng 		  - longitude of spot	
	 * lat 		  - latitude of spot
	 * price 	  - price expended 
	 * tips       - tips for this activity
	 * 
	 * @return Array
	 */
	protected $fillable = ['place_name', 'lng', 'lat', 'price', 'tips'];

	public function activity(){
		$this->morphMany('App\Activity','typable');
	}


	public function category()
	{
		return $this->belongsToMany('App\Category');
	}


	public function scopeHaversine($query, $lat, $lng, $max_distance = 25, $units = 'kilometers', $fields = false )
	{

		if(empty($lat)){
			$lat = 0;
		}
		if(empty($lng)){
			$lng = 0;
		}
		/*
         *  Allow for changing of units of measurement
         */
		switch ( $units ) {
			case 'miles':
				//radius of the great circle in miles
				$gr_circle_radius = 3959;
				break;
			case 'kilometers':
				//radius of the great circle in kilometers
				$gr_circle_radius = 6371;
				break;
		}
		/*
         *  Support the selection of certain fields
         */
		if( ! $fields ) {
			$fields = array( 'place_name', 'CONCAT(lng, " ", lat) as pos ', ' tips' );
		}
		/*
         *  Generate the select field for disctance
         */
		$distance_select = sprintf(
				"
					                ROUND(( %d * acos( cos( radians(%s) ) " .
				" * cos( radians( lat ) ) " .
				" * cos( radians( lng ) - radians(%s) ) " .
				" + sin( radians(%s) ) * sin( radians( lat ) ) " .
				" ) " .
				")
        							, 2 ) " .
				"AS distance
					                ",
				$gr_circle_radius,
				$lat,
				$lng,
				$lat
		);

		$data = $query->select( \DB::raw( implode( ',' ,  $fields ) . ',' .  $distance_select  ) )
				->having( 'distance', '<=', $max_distance )
				->orderBy( 'distance', 'ASC' )
				->get();

		//echo '<pre>';
		//echo $query->toSQL();
		//echo $distance_select;
		//echo '</pre>';
		//die();
		//
		//$queries = DB::getQueryLog();
		//$last_query = end($queries);
		//var_dump($last_query);
		//die();
		return $data;
	}
}
