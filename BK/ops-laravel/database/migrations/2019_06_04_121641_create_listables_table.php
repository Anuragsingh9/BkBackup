<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listablesls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('list_model_id')->unsigned()->index();
            $table->bigInteger('listablesls_id')->unsigned()->index();
            $table->string('listablesls_type');
            $table->timestamps();
            $table->softDeletes(); 

            $table->foreign('list_model_id')->references('id')->on('lists')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listablesls');
    }
}
