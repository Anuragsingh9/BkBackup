<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('consultation_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('consultation_step_id', false, true);
            $table->integer('consultation_question_type_id', false, true);
            $table->string('question');
            $table->string('description')->nullable();
            $table->tinyInteger('is_mandatory')->default(0);
            $table->tinyInteger('allow_add_other_answers')->default(0);
            $table->json('options')->nullable();
            $table->integer('order');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('consultation_step_id')->references('id')->on('consultation_steps')->onDelete('cascade');
            $table->foreign('consultation_question_type_id')->references('id')->on('consultation_question_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultation_questions');
    }
}
