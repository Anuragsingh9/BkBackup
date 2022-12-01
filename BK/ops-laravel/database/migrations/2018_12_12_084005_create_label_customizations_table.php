<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelCustomizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('label_customizations');
        Schema::create('label_customizations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('on_off')->default(0);
            $table->string('name')->nullable();
            $table->string('default_en')->nullable();
            $table->string('default_fr')->nullable();
            $table->string('custom_en')->nullable();
            $table->string('custom_fr')->nullable();
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
        Schema::dropIfExists('label_customizations');
    }
}
