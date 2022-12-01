<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewslttersBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_blocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('newsletter_id')->unsigned()->index();
            $table->bigInteger('template_block_id')->unsigned()->index();
            $table->text('blocks');
            $table->string('image_url',255);
            $table->integer('short_order');
            $table->timestamps();
            $table->softDeletes();

            //foreign key
            $table->foreign('newsletter_id')->references('id')->on('newsletters')->onDelete('cascade');
            $table->foreign('template_block_id')->references('id')->on('newsletter_template_blocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletters_blocks');
    }
}
