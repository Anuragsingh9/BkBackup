<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationStepsTable extends Migration
{
    public function up()
    {
        Schema::create('consultation_steps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consultation_sprint_id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('answerable')->default(1);
            $table->tinyInteger('step_type')->default(2)->comment('1= welcome step, 2= question step, 3= meeting step, 4= video step, 5= report step, 6= thank you step');
            $table->json('extra_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('consultation_sprint_id')->references('id')->on('consultation_sprints')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultation_steps');
    }
}
