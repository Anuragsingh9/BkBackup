<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInWorkshopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->json('setting')->nullable();
            $table->tinyInteger('is_qualification_workshop')->default(0)->nullable()->comment('0= it is a workshop or committee,1 =it is an active qualification committee,2 =it is a dormant qualification committee 
');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workshops', function (Blueprint $table) {
            //
        });
    }
}
