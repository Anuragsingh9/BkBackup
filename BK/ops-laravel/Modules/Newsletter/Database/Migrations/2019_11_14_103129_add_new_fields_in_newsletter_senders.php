<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsInNewsletterSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletter_senders', function (Blueprint $table) {
			$table->text('address')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('postal')->nullable();
			$table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletter_senders', function (Blueprint $table) {

        });
    }
}
