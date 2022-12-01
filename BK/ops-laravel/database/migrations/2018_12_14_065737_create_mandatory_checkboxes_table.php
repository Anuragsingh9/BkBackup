<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandatoryCheckboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mandatory_checkboxes')) {
            Schema::create('mandatory_checkboxes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('text_value')->nullable();
                $table->string('text_before_link');
                $table->string('text_after_link');
                $table->string('text_of_link');
                $table->boolean('target_blank')->default(false);
                $table->unsignedInteger('skill_id')->index();
                $table->timestamps();
            });

            Schema::table('mandatory_checkboxes', function ($table) {
                $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mandatory_checkboxes');
    }
}
