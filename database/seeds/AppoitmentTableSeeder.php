<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppoitmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // array of appoitment status
        $status = ['PENDING', 'APPROVED', 'REJECTED'];

        DB::table('appoitments')->insert([
            'id' => 1,
            'status' => "APPROVED",
            'start_time' => Carbon::now()->addMinutes(6000),
            'end_time' => Carbon::now()->addMinutes(6015),
            'service_provider_id' => 1,
            'client_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 2,
            'status' => "APPROVED",
            'start_time' => Carbon::now()->addMinutes(7045),
            'end_time' => Carbon::now()->addMinutes(7060),
            'service_provider_id' => 1,
            'client_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 3,
            'status' => "APPROVED",
            'start_time' => Carbon::now()->subMinutes(60),
            'end_time' => Carbon::now()->subMinutes(45),
            'service_provider_id' => 1,
            'client_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 4,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now()->subMinutes(15),
            'end_time' => Carbon::now(),
            'service_provider_id' => 1,
            'client_id' => 5,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 5,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now()->subMinutes(45),
            'end_time' => Carbon::now()->subMinutes(30),
            'service_provider_id' => 3,
            'client_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 6,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now()->subMinutes(45),
            'end_time' => Carbon::now()->subMinutes(30),
            'service_provider_id' => 4,
            'client_id' => 4,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 7,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addMinutes(15),
            'service_provider_id' => 4,
            'client_id' => 2,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 8,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addMinutes(15),
            'service_provider_id' => 5,
            'client_id' => 5,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 9,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now()->addMinutes(15),
            'end_time' => Carbon::now()->addMinutes(30),
            'service_provider_id' => 5,
            'client_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('appoitments')->insert([
            'id' => 10,
            'status' => $status[array_rand($status)],
            'start_time' => Carbon::now()->addMinutes(30),
            'end_time' => Carbon::now()->addMinutes(45),
            'service_provider_id' => 5,
            'client_id' => 3,
            'created_at' => Carbon::now(),
        ]);
    }
}
