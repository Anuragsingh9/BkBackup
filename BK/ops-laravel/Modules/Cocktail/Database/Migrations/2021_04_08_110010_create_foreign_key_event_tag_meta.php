<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyEventTagMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('event_tag_metas', function (Blueprint $table) {
            $table->foreign('tag_id', 'event_tag_relation_forign')->references('id')->on('event_tags')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'user_meta_tag_relation_forign')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('');
    }
}
