<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemoLiveAssetsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('demo_live_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_path')->nullable();
            $table->integer('asset_type')->comment('(1.YouTube) (2.Vimeo) (3.Images)');
            $table->string('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('demo_live_assets');
    }
}
