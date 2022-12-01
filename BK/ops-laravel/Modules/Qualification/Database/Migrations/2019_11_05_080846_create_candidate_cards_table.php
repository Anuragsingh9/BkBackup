<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id', false, true);
            $table->integer('final_by', false, true);
            $table->integer('workshop_id', false, true);
            $table->tinyInteger('card_instance');
            $table->tinyInteger('is_archived')->default(0);
            $table->text('card_no', 20);
            $table->text('reason_card_archived', 20)->nullable();
            $table->timestamp('date_of_validation', 0)->nullable()->comment('this the value of user_meta saved_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('final_by')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_cards');
    }
}
