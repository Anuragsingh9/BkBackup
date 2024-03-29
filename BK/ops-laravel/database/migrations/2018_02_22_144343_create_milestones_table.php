<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMilestonesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->string('label', 100)->nullable();
            $table->integer('user_id');
            $table->integer('end_date');
            $table->integer('color_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('milestones');
    }

}
