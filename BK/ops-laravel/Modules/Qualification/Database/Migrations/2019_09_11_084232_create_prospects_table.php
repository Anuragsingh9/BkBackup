<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fname', 50);
            $table->string('lname', 50);
            $table->string('tel', 20);
            $table->string('email')->unique();
            $table->string('company', 100);
            $table->string('reg_no', 100);
            $table->text('comment')->nullable();
            $table->string('zip_code', 20);
            $table->string('workshop_code', 20);
            $table->integer('sort_order', false, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospects');
    }
}
