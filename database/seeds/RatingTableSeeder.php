<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RatingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_provider_ratings')->insert([
            'comment' => "Excellent language and communication skills. was very useful!",
            'learning_rate' => 5.0,
            'teaching_rate' => 4.5,
            'good_communication_skills' => 4.0,
            'good_teaching_skills' => 4.0,
            'intersting_conserviation' => 5.0,
            'kind_personality' => 5.0,
            'correcting_my_language' => 5.0,
            'service_provider_id' => 1,
            'language_id' => 12,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_ratings')->insert([
            'comment' => "Good to learn but not happy with how the discussion went",
            'learning_rate' => 5.0,
            'teaching_rate' => 4.5,
            'good_communication_skills' => 3.0,
            'good_teaching_skills' => 3.0,
            'intersting_conserviation' => 3.0,
            'kind_personality' => 3.0,
            'correcting_my_language' => 3.0,
            'service_provider_id' => 1,
            'language_id' => 9,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_ratings')->insert([
            'comment' => "Harvard is the best service provider ever",
            'learning_rate' => 5.0,
            'teaching_rate' => 5.0,
            'good_communication_skills' => 5.0,
            'good_teaching_skills' => 5.0,
            'intersting_conserviation' => 5.0,
            'kind_personality' => 5.0,
            'correcting_my_language' => 5.0,
            'service_provider_id' => 1,
            'language_id' => 90,
            'created_at' => Carbon::now(),
        ]);

        DB::table('service_provider_ratings')->insert([
            'comment' => "Service can be improved to better language and time.",
            'learning_rate' => 2.5,
            'teaching_rate' => 2.5,
            'good_communication_skills' => 2.5,
            'good_teaching_skills' => 2.5,
            'intersting_conserviation' => 2.5,
            'kind_personality' => 2.5,
            'correcting_my_language' => 2.5,
            'service_provider_id' => 1,
            'language_id' => 68,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "good",
            'learning_rate' => 5.0,
            'teaching_rate' => 4.5,
            'good_communication_skills' => 4.0,
            'good_teaching_skills' => 3.5,
            'intersting_conserviation' => 3.5,
            'kind_personality' => 3.5,
            'correcting_my_language' => 3.5,
            'service_provider_id' => 2,
            'language_id' => 12,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "very good",
            'learning_rate' => 5.0,
            'teaching_rate' => 5.0,
            'good_communication_skills' => 5.0,
            'good_teaching_skills' => 5.0,
            'intersting_conserviation' => 5.0,
            'kind_personality' => 5.0,
            'correcting_my_language' => 5.0,
            'service_provider_id' => 3,
            'language_id' => 40,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "this provider is very good",
            'learning_rate' => 5.0,
            'teaching_rate' => 4.5,
            'good_communication_skills' => 4.0,
            'good_teaching_skills' => 4.0,
            'intersting_conserviation' => 5.0,
            'kind_personality' => 5.0,
            'correcting_my_language' => 5.0,
            'service_provider_id' => 3,
            'language_id' => 12,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "very bad",
            'learning_rate' => 1.0,
            'teaching_rate' => 1.0,
            'good_communication_skills' => 1.0,
            'good_teaching_skills' => 1.0,
            'intersting_conserviation' => 1.0,
            'kind_personality' => 1.0,
            'correcting_my_language' => 1.0,
            'service_provider_id' => 4,
            'language_id' => 114,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "can be better",
            'learning_rate' => 2.0,
            'teaching_rate' => 2.0,
            'good_communication_skills' => 2.0,
            'good_teaching_skills' => 2.0,
            'intersting_conserviation' => 2.0,
            'kind_personality' => 2.0,
            'correcting_my_language' => 2.0,
            'service_provider_id' => 4,
            'language_id' => 119,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "",
            'learning_rate' => 5.0,
            'teaching_rate' => 4.5,
            'good_communication_skills' => 4.0,
            'good_teaching_skills' => 4.0,
            'intersting_conserviation' => 5.0,
            'kind_personality' => 5.0,
            'correcting_my_language' => 5.0,
            'service_provider_id' => 5,
            'language_id' => 32,
            'created_at' => Carbon::now(),
        ]);
        DB::table('service_provider_ratings')->insert([
            'comment' => "excelent",
            'learning_rate' => 5.0,
            'teaching_rate' => 4.5,
            'good_communication_skills' => 4.0,
            'good_teaching_skills' => 4.0,
            'intersting_conserviation' => 5.0,
            'kind_personality' => 5.0,
            'correcting_my_language' => 5.0,
            'service_provider_id' => 5,
            'language_id' => 47,
            'created_at' => Carbon::now(),
        ]);
    }
}
