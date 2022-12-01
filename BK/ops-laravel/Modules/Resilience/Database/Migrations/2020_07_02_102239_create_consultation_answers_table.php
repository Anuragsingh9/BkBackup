<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('consultation_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('consultation_uuid')->index();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('user_workshop_id');
            $table->unsignedBigInteger('consultation_question_id');
            $table->json('answer')->nullable();
            $table->json('manual_answer')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('consultation_uuid')->references('uuid')->on('consultations')->onDelete('cascade');
            $table->foreign('consultation_question_id')->references('id')->on('consultation_questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultation_answers');
    }
}
