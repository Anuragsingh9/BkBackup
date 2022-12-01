<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSpace extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('event_space', function (Blueprint $table) {
            $table->uuid('space_uuid');
            $table->primary('space_uuid');
            $table->string('space_name')->nullable();
            $table->string('space_short_name')->nullable();
            $table->string('space_mood')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->string('space_image_url')->nullable();
            $table->string('space_icon_url')->nullable();
            $table->tinyInteger('is_vip_space')->default(0);
            $table->json('opening_hours')->default('{}');
            $table->unsignedInteger('event_id');
            $table->string('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('event_space');
    }
}
