<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranscribeTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transcribe_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedInteger('user_id');
            $table->string('type')->comment('(1, Notes), (2, Assistance');
            $table->integer('time_used');
            $table->dateTime('used_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transcribe_logs');
    }
}
