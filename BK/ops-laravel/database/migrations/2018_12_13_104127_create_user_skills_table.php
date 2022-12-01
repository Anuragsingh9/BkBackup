<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_skills')) {
            Schema::create('user_skills', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->index();
                $table->unsignedInteger('skill_id')->index();
                $table->integer('created_by')->index();
                $table->integer('mandatory_checked_by')->index();
                $table->timestamp('mandatory_checked_at', 0)->nullable();
                //fields as per type fill one at a time
                $table->string('checkbox_input')->nullable();
                $table->string('yes_no_input', 5)->nullable();
                $table->string('scale_5_input', 5)->nullable();
                $table->string('scale_10_input', 5)->nullable();
                $table->string('percentage_input', 5)->nullable();
                $table->string('numerical_input')->nullable();
                $table->text('text_input')->nullable();
                $table->text('comment_text_input')->nullable();
                $table->text('address_text_input')->nullable();
                $table->longText('long_text_input')->nullable();
                $table->string('file_input')->nullable();
                $table->string('mandatory_file_input')->nullable();
                $table->string('mandatory_checkbox_input')->nullable();
                $table->integer('select_input');
                $table->timestamp('date_input', 0)->nullable();
                $table->timestamps();
            });

            Schema::table('user_skills', function ($table) {
                $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            });

            Schema::table('user_skills', function ($table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_skills');
    }
}
