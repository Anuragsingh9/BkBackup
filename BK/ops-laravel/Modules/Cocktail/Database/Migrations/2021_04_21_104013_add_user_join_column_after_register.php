<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserJoinColumnAfterRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_user_data', function (Blueprint $table) {
            $table->integer('is_joined_after_reg')->default(1)->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_user_data', function (Blueprint $table) {
            $table->dropColumn('is_joined_after_reg');
        });
    }
}
