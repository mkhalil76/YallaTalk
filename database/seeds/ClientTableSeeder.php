<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gender = ['M','F','O'];
        DB::table('clients')->insert([
            'id' => 1,
            'birth_of_date' => "1992-12-12",
            'short_bio' => "I would like to learn more by this platform. I am always looking to learn new things and exchange skills",
            'account_status' => 1,
            'hobbis' => json_encode(["Swimming", "Reading", "playing sqash"]),
            'show_last_name' => 1,
            'gender' => "F",
            'organization' => 0,
            'availability' => 1,
            'user_id' => 1
        ]);

        DB::table('clients')->insert([
            'id' => 2,
            'birth_of_date' => "1993-01-10",
            'short_bio' => "real madried",
            'account_status' => 1,
            'hobbis' => json_encode(["play fotball"]),
            'show_last_name' => 1,
            'gender' => $gender[array_rand($gender)],
            'organization' => 0,
            'availability' => 1,
            'user_id' => 2
        ]);

        DB::table('clients')->insert([
            'id' => 3,
            'birth_of_date' => "1990-02-02",
            'short_bio' => "",
            'account_status' => 1,
            'hobbis' => json_encode(["flying"]),
            'show_last_name' => 1,
            'gender' => $gender[array_rand($gender)],
            'organization' => 0,
            'availability' => 1,
            'user_id' => 6
        ]);

        DB::table('clients')->insert([
            'id' => 4,
            'birth_of_date' => "1991-05-04",
            'short_bio' => "",
            'account_status' => 1,
            'hobbis' => json_encode(["swimming"]),
            'show_last_name' => 1,
            'gender' => $gender[array_rand($gender)],
            'organization' => 0,
            'availability' => 1,
            'user_id' => 9
        ]);

        DB::table('clients')->insert([
            'id' => 5,
            'birth_of_date' => "2000-06-04",
            'short_bio' => "",
            'account_status' => 1,
            'hobbis' => json_encode(["swimming"]),
            'show_last_name' => 1,
            'gender' => $gender[array_rand($gender)],
            'organization' => 0,
            'availability' => 1,
            'user_id' => 10
        ]);
    }
}
