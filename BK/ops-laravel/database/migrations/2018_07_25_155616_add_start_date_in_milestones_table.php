<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDateInMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('milestones', 'end_date')){
            if (Schema::hasColumn('milestones', 'start_date')){
                Schema::table('milestones', function (Blueprint $table) {
                    $table->dateTime('start_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
                    $table->dateTime('end_date')->nullable()->change();
                });
            }
            else{
                Schema::table('milestones', function (Blueprint $table) {
                    $table->dateTime('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                    $table->dateTime('end_date')->nullable()->change();
                });
            }

        }
        
        else{
            Schema::table('milestones', function (Blueprint $table) {
                $table->dateTime('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('end_date')->nullable()->change();
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
        //
    }
}
