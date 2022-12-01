<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class UpdateWorkshopForeignKeyToWorkshopTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::disableForeignKeyConstraints();

            Schema::table('workshops', function (Blueprint $table) {
                $table->dropForeign(['code1']);
                $table->dropIndex(['code1']);
            });
            Schema::table('workshop_codes', function (Blueprint $table) {
                $table->dropIndex(['code']);
            });
            Schema::table('workshop_codes', function (Blueprint $table) {
                $table->index(['code']);
            });

            Schema::table('workshops', function (Blueprint $table) {
                $table->index(['code1']);
                $table->foreign('code1')->references('code')->on('workshop_codes')->onDelete('cascade')->onUpdate('CASCADE');
            });
            Schema::enableForeignKeyConstraints();
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('workshop', function (Blueprint $table) {

            });
        }
    }
