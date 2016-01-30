<?php 
use Illuminate\Database\Seeder;

use Faker as Faker;

class EatTableSeeder extends Seeder {

    public function run()
    {
        DB::table('eats')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		DB::table('eats')->insert([
            'place_name'    => $faker->streetAddress,
            'price'         => $faker->randomNumber(2),
            'tips'          => $faker->text(20),
            'pos'           => $faker->latitude.",".$faker->longitude
        	]);
       	}   
    }
}
?>