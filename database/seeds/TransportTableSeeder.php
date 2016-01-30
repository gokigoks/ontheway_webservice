<?php 
use Illuminate\Database\Seeder;

use Faker as Faker;

class TransportTableSeeder extends Seeder {

    public function run()
    {
        DB::table('transports')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		DB::table('transports')->insert([
            'origin' => $faker->name,
            'destination' => $faker->email,
            'tips' => $faker->text(20)
        	]);

       	}

      
        
    }

}

?>