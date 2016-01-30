<?php 
use Illuminate\Database\Seeder;
use App\User;
use Faker as Faker;

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();
        $faker = Faker\Factory::create();

       	for($i=0; $i<5;$i++){
       		
       		DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => bcrypt('secret'),
        	]);



       	}

        DB::table('users')->insert([
            'name' => 'gokigoks',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('asdasd')
        ]);
        
    }

}

?>