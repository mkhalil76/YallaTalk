<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPackegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_packeges', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('packege_id')->unsigned();
            $table->date('join_date');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('packege_id')
                ->references('id')
                ->on('packeges')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->primary(['user_id', 'packege_id'], 'user_packeges_index');
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
        Schema::dropIfExists('user_packeges');
    }
}
