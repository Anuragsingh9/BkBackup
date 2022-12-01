<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_subscription_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('form_name',100);
            $table->bigInteger('list_id')->unsigned()->index();
            $table->string('success_url',100);
            $table->string('error_url',100);
            $table->boolean('display_header_zone');
            $table->string('title',100);
            $table->string('seperator_line_color');
            $table->boolean('field_email');
            $table->boolean('field_fname');
            $table->boolean('field_lname');
            $table->string('font_family',50);
            $table->string('font_size',100);
            $table->string('background_color');
            $table->string('button_color');
            $table->string('button_text_color',191);
            $table->string('rounded_button',20);
            $table->string('button_text');
            $table->longText('html_code')->nullable();
            $table->timestamps();
            $table->softDeletes(); 

            //foriegn keys

            $table->foreign('list_id')->references('id')->on('lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletter_subscription_forms');
    }
}
