<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogConvoStateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_convo_state_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('convo_state_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_grade')->nullable();
            $table->timestamps();

            $table->foreign('convo_state_id')->on('log_convo_sub_state')->references('id')->cascadeOnDelete();
            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('user_grade')->on('roles')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_convo_state_user');
    }
}
