<?php 
use Illuminate\Database\Seeder;
use App\Interests;
use Faker as Faker;

class InterestsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('interests')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		$interest = new Interests;
          $interest->interest_name = $faker->text(20);
          $interest->save();
       		// DB::table('interests')->insert([
         //    'interest_name'   => $faker->text(20)
        	// ]);

       	}   
    }
}
?>