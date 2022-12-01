<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',255);
            $table->text('header_text');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('start_time');
			$table->time('end_time');
            $table->text('address')->comment('google map autocomplete');
            $table->string('city',80)->comment('auto fill from google map address with editable option');
            $table->text('image')->nullable(); 
            $table->enum('type',['int','ext'])->default('ext');
            $table->integer('workshop_id')->nullable()->comment('workshop id of event, used only fo internal events');
            $table->unsignedInteger('created_by_user_id')->comment('the user who created the event');
            $table->bigInteger('wp_post_id')->nullable()->comment('wordpress post id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('created_by_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_info');
    }
}
