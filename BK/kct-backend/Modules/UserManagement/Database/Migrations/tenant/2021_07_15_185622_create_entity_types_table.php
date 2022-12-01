<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityTypesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('entity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('level');
            $table->unsignedBigInteger('parent')->nullable();
            $table->foreign('parent')->references('id')->on('entity_types')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('entity_types');
    }
}
