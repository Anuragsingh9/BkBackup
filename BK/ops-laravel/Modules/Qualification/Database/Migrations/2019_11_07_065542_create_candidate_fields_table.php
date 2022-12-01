<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('qualification_field_id')->unsigned()->index();
            $table->integer('user_id', false, true);
            $table->timestamps();

            $table->foreign('qualification_field_id')->references('id')->on('qualification_fields')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_fields');
    }
}
