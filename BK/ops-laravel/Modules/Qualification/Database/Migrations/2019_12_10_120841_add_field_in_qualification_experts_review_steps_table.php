<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldInQualificationExpertsReviewStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qualification_experts_review_steps', function (Blueprint $table) {
            $table->tinyInteger('for_card_instance')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualification_experts_review_steps', function (Blueprint $table) {
            $table->tinyInteger('for_card_instance')->default(1);
        });
    }
}
