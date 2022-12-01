<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_schedule_timings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sender_id')->unsigned()->index();
            $table->bigInteger('newsletter_id')->unsigned()->index();
            
            $table->dateTime('schedule_time');
            $table->timestamps();
            $table->softDeletes(); 

            //foriegn keys
            $table->foreign('sender_id')->references('id')->on('newsletter_senders')->onDelete('cascade');
            $table->foreign('newsletter_id')->references('id')->on('newsletters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletters_schedule_timings');
    }
}
