<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100);
            $table->text('description')->nullable();
            $table->integer('type')->comment('0->Internal 1->External list');
            $table->bigInteger('typology_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
            //foriegn keys
            $table->foreign('typology_id')->references('id')->on('newsletter_typology')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lists');
    }
}
