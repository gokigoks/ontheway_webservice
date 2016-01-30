<?php 
use Illuminate\Database\Seeder;
use App\Route;
use App\Stop;
use App\Hop;
use App\Segment;
use App\Iterinary;
use App\FlightIterinary;
use Faker as Faker;

class RouteTableSeeder extends Seeder {

    public function run()
    {
        DB::table('routes')->delete();

        $faker = Faker\Factory::create();

        $data =  \Rome2RioData::call('cebu','manila');

       	foreach($data->routes as $route){
       		$new_route = new App\Route;              
          $new_route->name = $route->name;
          $new_route->distance = $route->distance;
          $new_route->duration = $route->duration;
          $new_route->price = \Rome2RioData::getRome2RioPrice($route); //ctrl + p and look for rome2riodata in facades folder            
          $new_route->save();
          
          $i = 1;

            foreach ($route->stops as $stop) {
              $stopObj = new Stop;
              $stopObj->name = $stop->name;
              $stopObj->kind = $stop->kind;
              $stopObj->pos = $stop->pos;
              $stopObj->tips = $faker->text(20);
              $stopObj->timezone = property_exists($stop, "timeZone") ? $stop->timeZone : "";
              $stopObj->region_code = property_exists($stop, "regionCode") ? $stop->regionCode : "";
              $stopObj->save();
              unset($stopObj);
            }

          /**
           * Loop here kay each routes naay segments
           *  
           */
          foreach ($route->segments as $segment) {
           
           //check if the segment is flight then passes it to the
           //converting function
           //the function then returns a generic segment object
             if($segment->kind == "flight"){

               $segment = \Rome2RioData::convertToFlightSegment($route,$segment);              

             }

           $new_segment = new App\Segment;
           $new_segment->mode = $segment->kind;
           $new_segment->route_id = $new_route->id;
           $new_segment->sequence = $i;
           $new_segment->origin_name = $segment->sName;
           $new_segment->destination_name = $segment->tName;
           $new_segment->origin_pos = $segment->sPos;
           $new_segment->destination_pos = $segment->tPos;
           $new_segment->price = \Rome2RioData::getRome2RioPrice($segment); //ctrl + p and look for rome2riodata in facades folder
           $new_segment->path = (property_exists($segment, 'path')) ? $segment->path : '';
           $new_segment->distance = $segment->distance;
           $new_segment->duration = $segment->duration;
           $new_segment->save();
           $new_segment->route()->associate($new_segment); // $new_segment->save() sad diay

           $fi = new FlightIterinary;
           $fi->days = $new_segment->itineraties;

           unset($new_segment);
           $i++;
         }

          //unset variables kada human loop 
          unset($new_route, $i);
       	}
      
        
    }

}

?>