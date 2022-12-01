<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnListOrderActvityStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('activity_statuses', function (Blueprint $table) {          
         $table->integer('list_order', false, true)->length(10)->nullable();
       });
    }

    /**
-     * Reverse the migrations.
-     *
-     * @return void
-     */
    public function down()
    {
        Schema::table('activity_statuses', function (Blueprint $table) {
            $table->dropColumn('list_order');
         });
    }
}