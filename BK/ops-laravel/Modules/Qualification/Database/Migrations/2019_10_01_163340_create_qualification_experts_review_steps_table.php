<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualificationExpertsReviewStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_experts_review_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('step_id', false, true)->index();
            $table->tinyInteger('opinion')->nullable()->comment('0->Green,1->Yellow,2->Red');
            $table->text('opinion_text')->nullable();
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
        Schema::dropIfExists('qualification_experts_review_steps');
    }
}
