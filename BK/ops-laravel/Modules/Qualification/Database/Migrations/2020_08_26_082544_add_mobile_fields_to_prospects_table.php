<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class AddMobileFieldsToProspectsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('prospects', function (Blueprint $table) {
                if (!Schema::hasColumn('prospects', 'mobile')) {
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
            Schema::table('prospects', function (Blueprint $table) {
                $table->dropColumn('mobile');
            });
        }
    }
