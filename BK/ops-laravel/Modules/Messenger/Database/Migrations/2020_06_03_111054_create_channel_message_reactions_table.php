<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelMessageReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('im_message_reactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('message_id');
            $table->tinyInteger('reaction_type')->comment('(1-Star),(2-Like)');
            $table->unsignedInteger('reacted_by');
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
        Schema::dropIfExists('im_message_reactions');
    }
}
