<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('entity_id');
            $table->integer('created_by');
            $table->string('entity_label',100)->nullable();
            $table->timestamps();
           // $table->index('entity_id');
           // $table->index('user_id');
           // $table->index('created_by');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_users');
    }
}
