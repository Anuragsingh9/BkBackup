<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultProjectFieldInProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('projects', ['is_default_project'])){
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('is_default_project')->default(0);
            $table->dateTime('end_date')->nullable();
        });
    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
}
