<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

if (!Schema::hasTable('select_options')) {     
   Schema::create('select_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('option_value');
            $table->unsignedInteger('skill_id')->index();
            $table->timestamps();
        });

        Schema::table('select_options', function($table) {
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });

    }
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('select_options');
    }
}
