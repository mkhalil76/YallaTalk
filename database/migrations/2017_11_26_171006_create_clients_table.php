<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->binary('image')->nullable();
            $table->date('birth_of_date')->nullable();
            $table->text('short_bio')->nullable();
            $table->tinyInteger('account_status')->default(1)->comment('1 for active / 2 for frozen / 3 for blocked');
            $table->text('hobbis')->nullable();
            $table->boolean('show_last_name')->default(true)->comment('true for show / false hide');
            $table->char('gender', 1)->nullable()->comment('M for men / F for women / O for other');
            $table->boolean('organization')->default(false);
            $table->boolean('availability')->default(true)->comment('true for online / false offline');
            $table->string('organization_url')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('clients');
    }
}
