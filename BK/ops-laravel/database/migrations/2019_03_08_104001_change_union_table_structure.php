<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUnionTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unions', function (Blueprint $table) {
            $table->string('address1')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('union_code',50)->change();
            $table->string('city')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('telephone')->nullable()->change();
            $table->string('union_name',200)->change();
            $table->string('contact_button',100)->nullable()->change();
            $table->integer('family_id')->nullable()->change();
            $table->integer('industry_id')->nullable()->change();
            $table->string('fax')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->text('union_description')->nullable()->change();
        });
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
