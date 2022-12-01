<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationsTable extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('consultations', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->primary('uuid');
            $table->integer('workshop_id')->index();
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->string('internal_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('display_results_until')->nullable();
            $table->tinyInteger('has_welcome_step')->default(0);
            $table->tinyInteger('is_reinvent')->default(0);
            $table->tinyInteger('public_reinvent')->default(0);
            $table->tinyInteger('allow_to_go_back')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            Schema::enableForeignKeyConstraints();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultations');
    }
}
