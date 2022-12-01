<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualificationFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_fields', function (Blueprint $table) {
            //id of field
            $table->bigIncrements('id');
            // step_id from qualification_steps_table as foreign key
            $table->bigInteger('step_id')->unsigned()->index();
            // field_id from custom_fields table as foreign key
            $table->Integer('field_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();

            //foreign keys
            $table->foreign('step_id')->references('id')->on('qualification_steps')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_fields');
    }
}
