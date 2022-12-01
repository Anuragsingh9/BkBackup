<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupSettingsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('group_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key');
            $table->json('setting_value');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->integer('follow_organisation')->default(0);
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('group_settings');
    }
}
