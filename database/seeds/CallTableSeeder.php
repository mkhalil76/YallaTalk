<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CallTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('calls')->insert([
            'id' => 1,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMinutes(15),
            'call_type' => 2,
            'client_id' => 1,
            'service_provider_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('calls')->insert([
            'id' => 2,
            'start_at' => Carbon::now()->addMinutes(30),
            'end_at' => Carbon::now()->addMinutes(45),
            'call_type' => 1,
            'client_id' => 1,
            'service_provider_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('calls')->insert([
            'id' => 3,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMinutes(15),
            'call_type' => 2,
            'client_id' => 1,
            'service_provider_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('calls')->insert([
            'id' => 4,
            'start_at' => Carbon::now()->subMinutes(30),
            'end_at' => Carbon::now()->subMinutes(15),
            'call_type' => 2,
            'client_id' => 4,
            'service_provider_id' => 2,
            'created_at' => Carbon::now(),
        ]);

        DB::table('calls')->insert([
            'id' => 5,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMinutes(15),
            'call_type' => 2,
            'client_id' => 3,
            'service_provider_id' => 3,
            'created_at' => Carbon::now(),
        ]);
    }
}
