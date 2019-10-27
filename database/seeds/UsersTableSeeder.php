<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'first_name' => "John",
            'last_name' => "Smith",
            'email' => "John@gmail.com",
            'provider' => "YALLATALK",
            'password' => bcrypt('password'),
            'mobile' => "+972569918245",
            'address1' => "3044 Cerullo Road" ,
            'address2' => "Louisville, KY 40216",
            'country' => "Tunisia",
            'invitation_code' => str_random(6),
            'user_type' => 1,
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'first_name' => "Deborah E",
            'last_name' => "McKenzie",
            'email' => "Deborah@harvard.com",
            'provider' => "YALLATALK",
            'password' => bcrypt('password'),
            'mobile' => "001909-463-2497",
            'address1' => "4284 Gordon Street" ,
            'address2' => "Etiwanda, CA 91739",
            'country' => "Swizerland",
            'invitation_code' => str_random(6),
            'user_type' => 2,
        ]);

        DB::table('users')->insert([
            'id' => 3,
            'first_name' => "muna",
            'last_name' => "ahmad",
            'email' => "muna2@gmail.com",
            'provider' => "TWITTER",
            'password' => bcrypt('secret'),
            'mobile' => "00972569918245",
            'address1' => "ramallah main street" ,
            'address2' => "",
            'country' => "palestine",
            'invitation_code' => str_random(6),
            'user_type' => 2,
        ]);

        DB::table('users')->insert([
            'id' => 4,
            'first_name' => "abeer",
            'last_name' => "adnan",
            'email' => "abeer@gmail.com",
            'provider' => "GOOGLE+",
            'password' => bcrypt('secret'),
            'mobile' => "0592923343",
            'address1' => "hebron main street" ,
            'address2' => "",
            'country' => "palestine",
            'invitation_code' => str_random(6),
            'user_type' => 2,
        ]);

        DB::table('users')->insert([
            'id' => 5,
            'first_name' => "dawoud",
            'last_name' => "abed",
            'email' => "dawoud@gmail.com",
            'provider' => "YALLATLAK",
            'password' => bcrypt('secret'),
            'mobile' => "0592928343",
            'address1' => "hebron main street" ,
            'address2' => "",
            'country' => "palestine",
            'invitation_code' => str_random(6),
            'user_type' => 2,
        ]);

        DB::table('users')->insert([
            'id' => 6,
            'first_name' => "mohammed",
            'last_name' => "saleem",
            'email' => "msaleem@gmail.com",
            'provider' => "YALLATLAK",
            'password' => bcrypt('secret'),
            'mobile' => "0597516245",
            'address1' => "tulkarem main street" ,
            'address2' => "",
            'country' => "palestine",
            'invitation_code' => str_random(6),
            'user_type' => 1,
        ]);

        DB::table('users')->insert([
            'id' => 7,
            'first_name' => "adbelhadi",
            'last_name' => "salem",
            'email' => "adbelhadi@gmail.com",
            'provider' => "YALLATLAK",
            'password' => bcrypt('secret'),
            'mobile' => "0598526247",
            'address1' => "jenen main street" ,
            'address2' => "",
            'country' => "palestine",
            'invitation_code' => str_random(6),
            'user_type' => 2,
        ]);

        DB::table('users')->insert([
            'id' => 8,
            'first_name' => "noor",
            'last_name' => "abed",
            'email' => "noor@yahoo.com",
            'provider' => "YALLATLAK",
            'password' => bcrypt('secret'),
            'mobile' => "0592527247",
            'address1' => "irbid main street" ,
            'address2' => "",
            'country' => "jordan",
            'invitation_code' => str_random(6),
            'user_type' => 2,
        ]);

        DB::table('users')->insert([
            'id' => 9,
            'first_name' => "amjad",
            'last_name' => "mohammed",
            'email' => "amjad@yahoo.com",
            'provider' => "FACEBOOK",
            'password' => bcrypt('secret'),
            'mobile' => "0599529247",
            'address1' => "street 22" ,
            'address2' => "",
            'country' => "dubai",
            'invitation_code' => str_random(6),
            'user_type' => 1,
        ]);

        DB::table('users')->insert([
            'id' => 10,
            'first_name' => "aseel",
            'last_name' => "khaled",
            'email' => "aseel@hotmail.com",
            'provider' => "GOOGLE+",
            'password' => bcrypt('secret'),
            'mobile' => "0599929212",
            'address1' => "alquds street" ,
            'address2' => "",
            'country' => "palestine",
            'invitation_code' => str_random(6),
            'user_type' => 1,
        ]);
    }
}
