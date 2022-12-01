<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationQuestionTypesTable extends Migration
{
    public function up()
    {
        Schema::create('consultation_question_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question_type');
            $table->tinyInteger('is_enable')->default(0);
            $table->tinyInteger('show_add_allow_button')->default(0);
            $table->json('format');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultation_question_types');
    }
}
