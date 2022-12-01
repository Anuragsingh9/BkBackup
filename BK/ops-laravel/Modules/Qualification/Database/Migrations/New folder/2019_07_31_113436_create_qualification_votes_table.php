<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualificationVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            // vote_id from votes table as foreign key
            $table->bigInteger('vote_id')->unsigned()->index();
            // item_id from vote_items table as foreign key
            $table->bigInteger('item_id')->unsigned()->index();
            // candidate_id from candidates table as foreign key
            $table->bigInteger('candidate_id')->unsigned()->index();
             // voter_id from voters table as foreign key
            $table->bigInteger('voter_id')->unsigned()->index();
            $table->string('comment',191);
            $table->timestamps();
            $table->softDeletes(); 

            $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('vote_items')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->foreign('voter_id')->references('id')->on('voters')->onDelete('cascade');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_votes');
    }
}
