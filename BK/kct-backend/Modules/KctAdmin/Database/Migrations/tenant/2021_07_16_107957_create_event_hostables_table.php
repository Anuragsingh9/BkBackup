<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventHostablesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_hostables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('host_id');
            $table->uuid('hostable_uuid');
            $table->string('hostable_type', 191);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('host_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_hostables');
    }
}
