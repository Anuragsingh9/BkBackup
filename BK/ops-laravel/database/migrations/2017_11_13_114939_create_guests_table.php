<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //Schema::drop('guests');
        Schema::create('guests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('meeting_id');
            $table->integer('workshop_id');
            $table->enum('url_type', ['doodle', 'prepd','repd']);
            $table->string('identifier', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('guests');
    }

}
