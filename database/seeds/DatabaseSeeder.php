<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ClientTableSeeder::class,
            ProviderTableSeeder::class,
            TopicTableSeeder::class,
            LanguageTableSeeder::class,
            AppoitmentTableSeeder::class,
            CallTableSeeder::class,
            ClientLanguegeTableSeeder::class,
            ProviderLanguegeTableSeeder::class,
            RatingTableSeeder::class,
            ProviderTopicTableSeeder::class
        ]);
    }
}
