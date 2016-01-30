<?php 
use Illuminate\Database\Seeder;
use App\Activity;
use Faker as Faker;

class ActivityTableSeeder extends Seeder {

    public function run()
    {
        DB::table('activities')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		DB::table('activities')->insert([
            'day_id'		=> $faker->randomDigit,
            'start_time'	=> $faker->time,
            'end_time'		=> $faker->time,
            'typable_type'	=> $faker->word,
            'typable_id'	=> $faker->randomDigit
        	]);

       	}   
    }
}
?>