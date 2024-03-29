<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsNewsletterRelationTable extends Migration {
    /**
     * @return void
     */
    public function up() {
        Schema::create('news_newsletter', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('news_id');
            $table->unsignedInteger('newsletter_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * @return void
     */
    public function down() {
        Schema::dropIfExists('news_newsletter');
    }
}
