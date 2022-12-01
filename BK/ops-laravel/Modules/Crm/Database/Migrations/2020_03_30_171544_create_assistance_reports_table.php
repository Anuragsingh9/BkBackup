<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssistanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistance_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->text('reports');
            $table->bigInteger('crm_assistance_type_id')->unsigned()->index();
            $table->integer('created_by')->unsigned()->index();
            $table->morphs('assistance_reportable','reportable_index');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('crm_assistance_type_id')->references('id')->on('crm_assistance_type')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistance_reports');
    }
}
