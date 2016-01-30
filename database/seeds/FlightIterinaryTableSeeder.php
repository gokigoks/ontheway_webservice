<?php 
use Illuminate\Database\Seeder;

use Faker as Faker;

class FlightIterinaryTableSeeder extends Seeder {

    public function run()
    {
        DB::table('flight_iterinaries')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		DB::table('flight_iterinaries')->insert([
            'segment_id'      => $faker->randomDigit,
            'days'            => $faker->randomDigit,
            'price'           => $faker->randomNumber(2)
        	]);
       	}   
    }
}
?>