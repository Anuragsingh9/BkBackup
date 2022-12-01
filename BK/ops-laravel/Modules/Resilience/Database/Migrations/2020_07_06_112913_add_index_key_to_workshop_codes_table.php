<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexKeyToWorkshopCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workshop_codes', function (Blueprint $table) {
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workshop_codes', function (Blueprint $table) {
            $table->dropForeign(['workshop_codes_code_index']);
            $table->dropIndex(['code']);
        });
    }
}
