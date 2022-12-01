<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hostname_id');
            $table->string('name_org')->nullable();
            $table->string('acronym')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();

            $table->foreign('hostname_id')->references('id')->on('hostnames')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('organisations');
    }
}
