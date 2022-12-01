<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class AddFieldsToQualificationClientsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('qualification_clients', function (Blueprint $table) {
                if (!Schema::hasColumn('qualification_clients', 'mobile')) {
                    $table->string('mobile', 25)->nullable();
                }
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('qualification_clients', function (Blueprint $table) {
                $table->dropColumn('mobile');
            });
        }
    }
