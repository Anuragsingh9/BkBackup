<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewslttersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('short_name');
            $table->text('description')->nullable();
            $table->string('url',500)->nullable();
            $table->bigInteger('sender_id')->unsigned()->index();
            $table->bigInteger('template_id')->nullable()->unsigned()->index();
            $table->text('html_code')->nullable();
            $table->timestamps();
            $table->softDeletes(); 


            //foriegn keys
            $table->foreign('sender_id')->references('id')->on('newsletter_senders')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('newsletter_templates')->onDelete('cascade');


        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletters');
    }
}
