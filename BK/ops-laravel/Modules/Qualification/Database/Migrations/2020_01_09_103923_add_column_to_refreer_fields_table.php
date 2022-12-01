<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToRefreerFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrer_fields', function (Blueprint $table) {
            $table->bigInteger('field_id')->nullable()->change();
            $table->bigInteger('step_id', false, true)->index();
            $table->tinyInteger('used')->default(0);
            $table->string('file')->nullable();
            $table->timestamp('uploaded_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referrer_fields', function (Blueprint $table) {

        });
    }
}
