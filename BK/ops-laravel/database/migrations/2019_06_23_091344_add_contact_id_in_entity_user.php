<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class AddContactIdInEntityUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('entity_users', function (Blueprint $table) {
            $table->bigInteger('contact_id', false, true)->nullable();
            $table->integer('user_id', false, true)->nullable()->change();
        });
//        Schema::table('entity_users', function ($table) {
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('contact_id')->references('id')->on('newsletter_contacts')->onDelete('cascade')->onUpdate('cascade');
//        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
