<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventUsersTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_pp_users_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag_EN')->nullable();
            $table->string('tag_FR')->nullable();
            $table->integer('tag_type')->comment('(1. Professional), (2. Personal)');
            $table->integer('status')->comment('(1. Accepted), (2. Rejected), (3. Pending)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_pp_users_tags');
    }
}
