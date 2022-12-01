<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRegOpenToEventMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_metas', function (Blueprint $table) {
            $table->integer('is_reg_open')->default(0)->after('share_agenda');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_metas', function (Blueprint $table) {
            $table->dropColumn('is_reg_open');
        });
    }
}
