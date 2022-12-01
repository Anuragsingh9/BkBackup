<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssetsSettingsToSceneryCategoryAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scenery_category_assets', function (Blueprint $table) {
            $table->json('asset_settings')->nullable()->after('asset_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scenery_category_assets', function (Blueprint $table) {
            $table->dropColumn('asset_settings');
        });
    }
}
