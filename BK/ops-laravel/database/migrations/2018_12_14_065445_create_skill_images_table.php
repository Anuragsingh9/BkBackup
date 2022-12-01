<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('skill_images')) {

            Schema::create('skill_images', function (Blueprint $table) {
                $table->increments('id');
                $table->string('url');
                $table->string('text_before_link');
                $table->string('text_after_link');
                $table->string('text_of_link');
                $table->boolean('target_blank')->default(false);
                $table->unsignedInteger('skill_id')->index();
                $table->timestamps();
            });

            Schema::table('skill_images', function ($table) {
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
        Schema::dropIfExists('skill_images');
    }
}
