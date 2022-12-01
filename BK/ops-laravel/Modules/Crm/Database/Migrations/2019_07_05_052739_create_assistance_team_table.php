<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssistanceTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistance_team', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('assistance_type_id')->unsigned()->index();;
            $table->Integer('member_id')->unsigned()->index();;
            $table->timestamps();
            $table->softDeletes(); 


            //foriegn keys
            $table->foreign('assistance_type_id')->references('id')->on('crm_assistance_type')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistance_team');
    }
}
