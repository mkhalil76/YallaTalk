<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProviderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gender = ["M","F","O"];
        DB::table('service_providers')->insert([
            'id' => 1,
            'call_type' => 1,
            'rating' => 3.5,
            'birth_of_date' => "1992-8-11",
            'short_bio' => "As a service provider we provide training on English and French, you can try practicing with us and you decide.",
            'account_status' => 1,
            'hobbis' => json_encode(["Writing", "Reading", "Swimming", "Self Development" ]),
            'gender' => "M",
            'availability' => 1,
            'user_id' => 2,
        ]);

        DB::table('service_providers')->insert([
            'id' => 2,
            'call_type' => 2,
            'rating' => 4.0,
            'birth_of_date' => "1994-01-03",
            'short_bio' => "",
            'account_status' => 1,
            'hobbis' => json_encode(["play fotball","Play pc"]),
            'gender' => $gender[array_rand($gender)],
            'availability' => 1,
            'user_id' => 4,
        ]);

        DB::table('service_providers')->insert([
            'id' => 3,
            'call_type' => 2,
            'rating' => 4.0,
            'birth_of_date' => "1995-10-05",
            'short_bio' => "",
            'account_status' => 1,
            'hobbis' => json_encode(["drink coffee"]),
            'gender' => $gender[array_rand($gender)],
            'availability' => 1,
            'user_id' => 5,
        ]);

        DB::table('service_providers')->insert([
            'id' => 4,
            'call_type' => 1,
            'rating' => 2.5,
            'birth_of_date' => "1993-10-11",
            'short_bio' => "life is short",
            'account_status' => 1,
            'hobbis' => json_encode(["play XBOX"]),
            'gender' => $gender[array_rand($gender)],
            'availability' => 0,
            'user_id' => 7,
        ]);

        DB::table('service_providers')->insert([
            'id' => 5,
            'call_type' => 2,
            'rating' => 3.0,
            'birth_of_date' => "1988-02-01",
            'short_bio' => ":)",
            'account_status' => 1,
            'hobbis' => json_encode(["read newslatter"]),
            'gender' => $gender[array_rand($gender)],
            'availability' => 0,
            'user_id' => 8,
        ]);
    }
}
