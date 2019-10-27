<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_provider_ratings', function (Blueprint $table) {
            $table->renameColumn('learning_rate', 'call_rate');
            $table->renameColumn('teaching_rate', 'provider_rate');
            $table->integer('good_communication_skills')->default(0)->change();
            $table->integer('good_teaching_skills')->default(0)->change();
            $table->integer('intersting_conserviation')->default(0)->change();
            $table->integer('kind_personality')->default(0)->change();
            $table->integer('correcting_my_language')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
