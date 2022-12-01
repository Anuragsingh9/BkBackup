<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualiicationExpertsReviewFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_experts_review_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('step_id', false, true)->index();
            $table->bigInteger('field_id', false, true)->index();
            $table->tinyInteger('opinion')->comment('0->questionMark,1->checkMark,2->crossMark');
            $table->integer('user_id', false, true);
            $table->integer('opinion_by_user', false, true);
            $table->tinyInteger('opinion_by')->comment('0->Expert,1->WkAdmin,2->ORG');
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
        Schema::dropIfExists('qualiication_experts_review_fields');
    }
}
