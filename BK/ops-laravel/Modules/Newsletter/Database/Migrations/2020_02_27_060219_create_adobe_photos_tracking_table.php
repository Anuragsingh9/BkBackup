<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdobePhotosTrackingTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('adobe_photos_tracking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('adobe_photo_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('type')->comment('(1, Bought), (2, Used)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('adobe_photos_tracking');
    }
}
