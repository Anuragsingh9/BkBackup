<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCandidateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_cards', function (Blueprint $table) {
            $table->integer('review_done')->default(0);
            $table->json('setting')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_cards', function (Blueprint $table) {
            $table->integer('review_done')->default(0);
            $table->json('setting')->nullable();
        });
    }
}
