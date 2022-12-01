<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdobePhotosTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('adobe_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('adobe_photo_id');
            $table->dateTime('bought_at')->default(DB::raw('CURRENT_TIMESTAMP'));;
            $table->string('search_tag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('adobe_photos');
    }
}
