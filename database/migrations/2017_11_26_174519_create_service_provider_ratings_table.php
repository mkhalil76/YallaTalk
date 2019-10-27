<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceProviderRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_provider_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->text("comment")->default(null);
            $table->float('learning_rate')->default(0.0);
            $table->float('teaching_rate')->default(0.0);
            $table->float('good_communication_skills')->default(0.0);
            $table->float('good_teaching_skills')->default(0.0);
            $table->float('intersting_conserviation')->default(0.0);
            $table->float('kind_personality')->default(0.0);
            $table->float('correcting_my_language')->default(0.0);
            $table->integer('service_provider_id')->unsigned();
            // foreign key service provider
            $table->foreign('service_provider_id')
                ->references('id')
                ->on('service_providers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('language_id')->unsigned();
            // foreign key language id
            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_provider_ratings');
    }
}
