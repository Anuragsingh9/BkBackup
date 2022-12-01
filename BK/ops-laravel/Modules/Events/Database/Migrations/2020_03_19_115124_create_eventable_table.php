<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedInteger('eventable_id')->comment('id of organiser or user');
            $table->string('eventable_type')->comment('store it is organiser or user as organiser');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('event_id')->references('id')->on('event_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventables');
    }
}
