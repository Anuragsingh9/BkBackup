<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualificationStepsConditionalSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steps_conditional', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('step_id', false, true);
            $table->integer('conditional_checkbox_id', false, true);
            $table->boolean('is_checked')->default(0)->nullable();
            $table->timestamps();
        });


        Schema::table('steps_conditional', function ($table) {
            $table->foreign('step_id', 'step_id')->references('id')->on('qualification_steps')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('conditional_checkbox_id', 'conditional_checkbox_id')->references('id')->on('skills')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_steps_conditional_skills');
    }
}
