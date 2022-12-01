<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillTabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('skill_tabs')) {
            Schema::create('skill_tabs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('created_by')->index();
                $table->boolean('is_valid')->default(1)->comment('because we user will delete we will makeit isavaml id=no and will not display');
                $table->integer('is_news_interested')->default(0);
                $table->integer('is_locked')->default(0);
                $table->enum('skill_tab_format', ['IsYesNo', 'IsFreeFormat'])->default('IsFreeFormat');
                $table->integer('sort_order');
                $table->timestamps();
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
        Schema::dropIfExists('skill_tabs');
    }
}
