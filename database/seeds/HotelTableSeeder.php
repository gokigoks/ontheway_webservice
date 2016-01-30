<?php 
use Illuminate\Database\Seeder;
use App\Hotel;
use Faker as Faker;

class HotelTableSeeder extends Seeder {

    public function run()
    {
        DB::table('hotels')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
          $hotel = new Hotel;
          $hotel->hotel_name = $faker->text(20);
          $hotel->pos = $faker->latitude.','.$faker->longitude;
          $hotel->tips = $faker->text(20);
          $hotel->save();
       		// DB::table('hotels')->insert([
         //    'hotel_name'	=> $faker->text(20),
         //    'pos'	        => $faker->latitude.','.$faker->longitude,
         //    'tips'        => $faker->text(20)
        	// ]);
       	}   
    }
}
?>