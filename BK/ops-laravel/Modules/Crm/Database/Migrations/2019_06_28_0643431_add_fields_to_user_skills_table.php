<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUserSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_skills', function (Blueprint $table) {
            $table->dropForeign('user_skills_user_id_foreign');
            $table->dropIndex('user_skills_user_id_index');
        });

        // Conflict with change on same Blueprint instance
        Schema::table('user_skills', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->change();
            $table->integer('field_id')->nullable()->unsigned()->index();
            $table->string('type', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_skills', function (Blueprint $table) {
            $table->dropColumn('field_id');
            $table->dropColumn('type');
        });
    }
}
