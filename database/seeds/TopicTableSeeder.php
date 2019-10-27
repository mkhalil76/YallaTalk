<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TopicTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('topics')->insert([
            'id' => 1,
            'topic_name' => "Finance",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 2,
            'topic_name' => "Public",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 3,
            'topic_name' => "Business",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 4,
            'topic_name' => "Education",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 5,
            'topic_name' => "Philosophy",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 6,
            'topic_name' => "Culture",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 7,
            'topic_name' => "Entrepreneurship",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 8,
            'topic_name' => "Marketing",
            'created_at' => Carbon::now(),
        ]);

        DB::table('topics')->insert([
            'id' => 9,
            'topic_name' => "Social",
            'created_at' => Carbon::now(),
        ]);
    }
}
