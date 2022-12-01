<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefreerFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrer_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('field_id', false, true)->index();
            $table->integer('refreer_id', false, true)->index();
            $table->integer('candidate_id', false, true)->index();
            $table->tinyInteger('status')->default(1)->comment('cadidate chose for refreer form');
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
        Schema::dropIfExists('referrer_fields');
    }
}
