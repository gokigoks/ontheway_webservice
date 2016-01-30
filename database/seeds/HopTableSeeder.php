<?php 
use Illuminate\Database\Seeder;

use Faker as Faker;

class HopTableSeeder extends Seeder {

    public function run()
    {
        DB::table('hops')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		

       	}   
    }
}
?>