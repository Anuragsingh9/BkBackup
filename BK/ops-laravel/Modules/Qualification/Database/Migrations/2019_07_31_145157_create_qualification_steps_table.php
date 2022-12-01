<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualificationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_steps', function (Blueprint $table) {
            //id of step
            $table->bigIncrements('id');
            //name of step
            $table->string('name', 255);
            // description of steps
            $table->string('description', 255);
            // for select that step as conditional or not
            $table->boolean('is_conditional')->default(0);
            // for select that step is final or not
            $table->boolean('is_final_step')->default(0);
            $table->integer('sort_order', false, true);
            $table->string('button_text')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_steps');
    }
}
