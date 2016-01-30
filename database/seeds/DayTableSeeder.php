<?php 
use Illuminate\Database\Seeder;

use Faker as Faker;

class DayTableSeeder extends Seeder {

    public function run()
    {
        DB::table('days')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		DB::table('days')->insert([
            'iterinary_id'    => $faker->randomDigit,
            'day_no'          => $faker->randomDigit
        	]);

       	}   
    }
}
?>