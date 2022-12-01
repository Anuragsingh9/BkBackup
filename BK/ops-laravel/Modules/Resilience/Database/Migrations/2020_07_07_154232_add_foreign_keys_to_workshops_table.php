<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToWorkshopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('workshops', function (Blueprint $table) {
            $table->tinyInteger('is_dependent')->default(0);
            $table->index(['code1']);
            $table->foreign('code1')->references('code')->on('workshop_codes')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workshops', function (Blueprint $table) {
            
            $table->dropForeign(['workshops_code1_index']);
            $table->dropIndex(['code1']);
            $table->dropColumn(['is_dependent']);
        });
    }
}
