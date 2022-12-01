<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntityUnionFieldAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->text('entity_description')->nullable();
            $table->string('entity_logo')->nullable();
            $table->string('entity_website')->nullable();
            $table->integer('industry_id')->nullable();
            $table->integer('family_id')->nullable();
            $table->string('fax')->nullable();
            $table->tinyInteger('entity_ref_type')->default(0);
            $table->tinyInteger('is_internal')->default(0);
        });
        Schema::table('entities', function ($table) {
            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
