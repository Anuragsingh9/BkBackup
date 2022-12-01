<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('skills')) {
            Schema::create('skills', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('skill_tab_id')->index();
                $table->string('name');
                $table->string('short_name');
                $table->string('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_valid')->default(1);
                $table->integer('is_mandatory')->default(0);
                $table->integer('skill_format_id')->index();
                $table->integer('is_unique')->default(0);
                $table->text('comment')->nullable();
                $table->text('link_text')->nullable();
                $table->text('comment_link')->nullable();
                $table->integer('comment_target_blank')->default(1);
                $table->integer('sort_order');
                $table->timestamps();
            });

            Schema::table('skills', function ($table) {
                $table->foreign('skill_tab_id')->references('id')->on('skill_tabs')->onDelete('cascade');
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
        Schema::dropIfExists('skills');
    }

}
