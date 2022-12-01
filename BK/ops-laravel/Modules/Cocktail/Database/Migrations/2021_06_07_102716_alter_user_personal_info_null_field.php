<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserPersonalInfoNullField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_user_personal_info', function (Blueprint $table) {
            $table->string('field_1')->nullable()->change();
            $table->string('field_2')->nullable()->change();
            $table->string('field_3')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_user_personal_info', function (Blueprint $table) {
            $table->string('field_1')->nullable(false)->change();
            $table->string('field_2')->nullable(false)->change();
            $table->string('field_3')->nullable(false)->change();
        });
    }
}
