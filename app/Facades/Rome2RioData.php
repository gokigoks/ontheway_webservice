<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-01-11
 * Time: 9:51 PM
 */

namespace app\Facades;
use Illuminate\Support\Facades\Facade;

class Rome2RioData extends Facade
{	
	protected $data;
    //protected static function getFacadeAccessor() { return 'Rome2RioData'; }

	public function __construct($data){
		$this->data = $data;
	}

    public static function getData(){
    	dd('data?');
    }
    /**
     * get routes function
     * @param type|null $index 
     * @return json object || array
     */
    public static function getRoutes($data, $index = null){
    	
    	
    	if(property_exists($data, 'routes') ||$index != null)
    	{
    		return $data->routes[$index];
    	}
    	if(property_exists($data, 'routes') || $index )
    	{
    		return $data->segments;
    	}
    	else
    	{
    		//dd($data->routes);
    		return $data->routes;
    	}
    }
    
    /**
     * Get Segments Function
     * @param index object | default NULL
     * @return Object
     */
    public static function getSegments($data,$index = null)
    {
    	if(property_exists($data, 'segments') || $index != null)
    	{	//dd('has segments');
    		return $data->segments[$index];
    	}
    	if(!property_exists($data, 'segments') || $index )
    	{
    		return $data->segments;
    	}
    	else
    	{	//dd('nosegments');
    		return "error.. no segments";
    	}
    }

    /**
     * strip price from rome2rio dataset
     * @param type $data rome2rio json response object
     * @param type|null $index 
     * @return type
     */
    public static function getRome2RioPrice($data, $index = null)
    {
    	if(property_exists($data, 'indicativePrice'))
    	{	
    		$price = $data->indicativePrice;	
    		return (property_exists($price, 'nativePrice') ? $data->nativePrice : ($data->price * 42);
    		//eturn $data->indicativePrice->nativePrice;
    	}
    	if(property_exists($data, 'nativePrice'))
    }
		


    public function getStops()
    {		

    }

    public function save($type){

    }

}

?>