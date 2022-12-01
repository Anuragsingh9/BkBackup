<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class AddClassIdInEntityUsersTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('entity_users', function (Blueprint $table) {
                $table->bigInteger('consultation_sign_up_class_positions_id', FALSE, TRUE)->index('class_positions_index')->nullable();
                $table->foreign('consultation_sign_up_class_positions_id', 'class_positions__foreign')->references('id')->on('consultation_signup_class_positions')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('entity_users', function (Blueprint $table) {

            });
        }
    }
