<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConditionalSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditional_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conditional_field_id', false, true)->index();
            $table->integer('conditional_checkbox_id', false, true)->index();
            $table->boolean('is_checked')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::table('conditional_skills', function ($table) {
            $table->foreign('conditional_field_id')->references('id')->on('skills')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('conditional_checkbox_id')->references('id')->on('skills')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conditional_skills');
    }
}
