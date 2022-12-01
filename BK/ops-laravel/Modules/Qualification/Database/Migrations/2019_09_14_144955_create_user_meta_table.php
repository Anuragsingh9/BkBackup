<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true)->index();
            $table->bigInteger('current_step_id', false, true)->index()->default(0);
            $table->json('setting')->nullable();
            $table->timestamps();
        });


        Schema::table('user_metas', function ($table) {
            $table->foreign('user_id', 'user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('current_step_id', 'current_step_id')->references('id')->on('qualification_steps')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_meta');
    }
}
