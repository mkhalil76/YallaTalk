<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceProviderLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_provider_languages', function (Blueprint $table) {
            $table->integer('service_provider_id')->unsigned();
            $table->foreign('service_provider_id')
                ->references('id')
                ->on('service_providers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('language_id')->unsigned();
            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->primary(['service_provider_id', 'language_id'], 'service_provider_languages_index');
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
        Schema::dropIfExists('service_provider_languages');
    }
}
