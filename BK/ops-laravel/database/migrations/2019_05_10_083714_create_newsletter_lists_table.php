<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsletterListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_lists', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->index();
            $table->bigInteger('list_id')->unsigned()->index();
            $table->bigInteger('newsletter_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes(); 

            //foriegn keys

            $table->foreign('list_id')->references('id')->on('lists')->onDelete('cascade');
            $table->foreign('newsletter_id')->references('id')->on('newsletters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletter_lists');
    }
}
