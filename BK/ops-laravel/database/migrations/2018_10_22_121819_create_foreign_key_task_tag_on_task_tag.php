<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKeyTaskTagOnTaskTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_tags', function (Blueprint $table) {
           $table->foreign('tag_id', 'tag_relation_forign')->references('id')->on('tags')->onUpdate('NO ACTION')->onDelete('CASCADE');
       });
    }

    /**
     * Reverse the migrations.
    *
-     * @return void
-     */   public function down()   {       //
   }
}