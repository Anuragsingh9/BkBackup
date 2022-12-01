<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnIsfinalInMeeting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         
          Schema::table('meetings', function (Blueprint $table) {
            $table->boolean('is_prepd_final')->default(false);
            });
         
         
            Schema::table('meetings', function (Blueprint $table) {
                $table->boolean('is_repd_final')->default(false);
            });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('is_prepd_final')->default(false);
            $table->boolean('is_repd_final')->default(false);
        });
    }
}
