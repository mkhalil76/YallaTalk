<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProviderLanguegeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 1,
            'language_id' => 48,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 1,
            'language_id' => 57,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 1,
            'language_id' => 47,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 1,
            'language_id' => 56,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 2,
            'language_id' => 90,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 2,
            'language_id' => 100,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 2,
            'language_id' => 99,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 3,
            'language_id' => 63,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 3,
            'language_id' => 57,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 3,
            'language_id' => 112,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 4,
            'language_id' => 29,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 4,
            'language_id' => 25,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 4,
            'language_id' => 17,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 5,
            'language_id' => 106,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 5,
            'language_id' => 87,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_languages')->insert([
            'service_provider_id' => 5,
            'language_id' => 69,
            'created_at' => Carbon::now(),
        ]);
    }
}
