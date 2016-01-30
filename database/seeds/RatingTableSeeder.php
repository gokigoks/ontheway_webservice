<?php 
use Illuminate\Database\Seeder;
use App\Rating;
use Faker as Faker;

class RatingTableSeeder extends Seeder {

    public function run()
    {
        DB::table('ratings')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		$rating = new Rating;
          $rating->user_id = $i+1;
          $rating->value = $i;
          $rating->ratingable_id = $faker->randomDigit;
          $rating->ratingable_type = $faker->word;
          $rating->save();

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