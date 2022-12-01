<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddIsImportToMeetings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('meetings', ['is_import'])){
            Schema::table('meetings', function ($table) {
                $table->tinyInteger('is_import')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function ($table) {
            $table->dropColumn('is_import');
        });
    }
}
