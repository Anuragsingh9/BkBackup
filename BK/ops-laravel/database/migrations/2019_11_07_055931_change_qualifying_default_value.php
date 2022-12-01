<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeQualifyingDefaultValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('skills', 'is_qualifying')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->integer('is_qualifying')->default(1)->nullable()->comment('0 = non qualifying 1= qualifying at origin 2= qualifying at 1 year 3= qualifying at 4 years')->change();
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
