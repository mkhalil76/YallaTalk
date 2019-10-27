<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClientLanguegeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $proficency = ['excellent', 'intermediate', 'beginner'];
        DB::table('clients_languages')->insert([
            'Language_proficency' => "excellent",
            'client_id' => 1,
            'language_id' => 56,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => "intermediate",
            'client_id' => 1,
            'language_id' => 57,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => "intermediate",
            'client_id' => 1,
            'language_id' => 47,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 2,
            'language_id' => 98,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 2,
            'language_id' => 74,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 3,
            'language_id' => 37,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 3,
            'language_id' => 94,
            'created_at' => Carbon::now(),
        ]);

        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 4,
            'language_id' => 45,
            'created_at' => Carbon::now(),
        ]);
        
        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 4,
            'language_id' => 55,
            'created_at' => Carbon::now(),
        ]);
        
        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 5,
            'language_id' => 88,
            'created_at' => Carbon::now(),
        ]);
        
        DB::table('clients_languages')->insert([
            'Language_proficency' => $proficency[array_rand($proficency)],
            'client_id' => 5,
            'language_id' => 78,
            'created_at' => Carbon::now(),
        ]);
    }
}
