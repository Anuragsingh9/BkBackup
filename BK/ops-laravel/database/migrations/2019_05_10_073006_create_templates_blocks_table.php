<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_template_blocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('template_id')->unsigned()->index();
            $table->text('block_html_code');
            $table->string('image_url',255);
            $table->integer('sort_order');
            $table->timestamps();
            $table->softDeletes();

            //for. keys
            
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
        Schema::dropIfExists('newsletter_templates_blocks');
    }
}
