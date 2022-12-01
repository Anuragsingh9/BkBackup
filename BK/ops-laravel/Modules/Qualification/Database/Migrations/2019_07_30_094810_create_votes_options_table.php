<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use phpDocumentor\Reflection\Types\Integer;

class CreateVotesOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            // vote_id from votes table as foreign key
            $table->bigInteger('vote_id')->unsigned()->index();
            $table->string('option_name',255);
            $table->string('short_name',25);
            $table->string('description')->nullable();
            $table->string('option_color',255);
            $table->string('option_tip_text',255);
            $table->integer('short_order');
            $table->timestamps();
            $table->softDeletes();

              //foriegn keys
              $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes_options');
    }
}
