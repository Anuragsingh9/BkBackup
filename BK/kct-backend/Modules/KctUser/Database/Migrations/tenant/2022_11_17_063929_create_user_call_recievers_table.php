<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCallRecieversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_call_receivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_log_id');
            $table->unsignedBigInteger('receiver_id');

            $table->foreign('call_log_id')->on('user_calls_logs')->references('id')->cascadeOnDelete();
            $table->foreign('receiver_id')->on('users')->references('id')->cascadeOnDelete();

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
        Schema::dropIfExists('user_call_recievers');
    }
}
