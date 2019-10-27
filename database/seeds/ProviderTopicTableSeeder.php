<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProviderTopicTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 1,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 2,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 3,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 4,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 5,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 6,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 7,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 8,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 1,
            'topic_id' => 9,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 2,
            'topic_id' => 2,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_topics')->insert([
            'service_provider_id' => 5,
            'topic_id' => 3,
            'created_at' => Carbon::now(),
        ]);
    }
}
