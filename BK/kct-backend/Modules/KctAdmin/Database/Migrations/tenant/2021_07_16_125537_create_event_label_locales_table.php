<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventLabelLocalesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('label_locales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('label_id');
            $table->string('value', 100);
            $table->string('locale', 100);
            $table->timestamps();

            $table->foreign('label_id')->references('id')->on('labels')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('label_locales');
    }
}
