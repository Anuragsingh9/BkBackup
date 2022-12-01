<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualificationDomainCheckboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_domain_checkboxes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('step_id',false,true)->index();
            $table->integer('skill_id',false,true)->index();
            $table->timestamps();
        });
        Schema::table('qualification_domain_checkboxes', function($table)
        {
            $table->foreign('step_id')->references('id')->on('qualification_steps')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_domain_checkboxes');
    }
}
