<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventOrganisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_organisers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fname',80);
            $table->string('lname',80);
            $table->string('company',255);
            $table->text('image')->nullable();
            $table->string('email',255);
            $table->string('phone',18);
            $table->string('website',255);
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
        Schema::dropIfExists('event_organisers');
    }
}
