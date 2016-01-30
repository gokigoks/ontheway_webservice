<?php 
use Illuminate\Database\Seeder;

use Faker as Faker;

class IterinaryTableSeeder extends Seeder {

    public function run()
    {
        DB::table('iterinaries')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<20;$i++){
       		
       		DB::table('iterinaries')->insert([
            'destination' => $faker->city,
            'origin' => $faker->city,
        	]);

       	}       
    }
}

?>