<?php 
use Illuminate\Database\Seeder;
use App\Spot;
use Faker as Faker;

class SpotTableSeeder extends Seeder {

    public function run()
    {
        DB::table('spots')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		$spot = new Spot;
          $spot->place_name = $faker->streetName;
          $spot->pos = $faker->latitude.",".$faker->longitude;
          $spot->price = $faker->randomNumber(2);
          $spot->tips = $faker->text(20);
          $spot->save();

       		// DB::table('ratings')->insert([
         //    'user_id'         => $i,
         //    'value'           => $i,
         //    'ratingable_id'		=> $faker->randomDigit,
         //    'ratingable_type'	=> $faker->word
        	// ]);

       	}   
    }
}
?>