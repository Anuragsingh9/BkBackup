<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleFieldInMandatoryCheckbox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mandatory_checkboxes', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->boolean('type_of')->default(false)->comment('1=Mandatory acceptance 0=Mandatory checkbox');
        });
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
