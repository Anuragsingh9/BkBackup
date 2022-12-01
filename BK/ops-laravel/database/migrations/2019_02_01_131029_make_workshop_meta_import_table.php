<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeWorkshopMetaImportTable extends Migration
{
    public function up()
    {
        Schema::create('workshop_meta_temps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('workshop_id');
            $table->integer('user_id')->unsigned()->index('workshop_user_relation');
            $table->boolean('role')->default(0)->comment('0=member,1=president,2=validator');
            $table->integer('meeting_id')->nullable();;
            $table->datetime('updated_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            
        });
        Schema::table('workshop_meta_temps', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workshop_meta_temps');
    }
}
