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
	protected $routes, $agencies, $aircrafts, $airlines, $airports, $places, $serveTime;
    //protected static function getFacadeAccessor() { return 'Rome2RioData'; }

	public function __construct($object){
		$this->routes = $object->routes;
		$this->agencies = $object->agencies;
		$this->aircrafts = $object->aircrafts;
		$this->airlines = $object->airlines;
		$this->places = $object->places;
		$this->serveTime = $object->serveTime;
	}

    public static function getData(){
    	dd('data?');
    }
    /**
     * get routes function
     * @param type|null $index 
     * @return json object || array
     */
    public function getRoutes($index = null){
    	
    	
    	if($index != null)
    	{
    		return $this->routes[$index];
    	}
    	else
    	{
    		return $this->routes;
    	}
    }
    /**
     * Get Segments Function
     * 
     * @return Object
     */
    public function getSegments($index = null)
    {
    	if($index != null)
    	{
    		return $this->routes[$index];
    	}
    	else
    	{
    		return $this->routes;
    	}
    }

    public function getStops()
    {

    }    
}

?>