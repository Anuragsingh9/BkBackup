<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnionMemberEntityUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_users' , function(Blueprint $table) {
            $table->tinyInteger( 'membership_type')->nullable()->comment('currently using for unions only (0, member) and (1, staff)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entity_users' , function(Blueprint $table) {
            $table->dropColumn(['membership_type']);
        });
    }
}
