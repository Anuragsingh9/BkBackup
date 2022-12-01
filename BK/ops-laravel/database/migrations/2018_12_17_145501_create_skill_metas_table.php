<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
 if (!Schema::hasTable('skill_metas')) {
        Schema::create('skill_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('skill_id')->index();
            $table->longText('value');
            $table->timestamps();
        });

        Schema::table('skill_metas', function ($table) {
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
        Schema::dropIfExists('skill_metas');
    }
}
