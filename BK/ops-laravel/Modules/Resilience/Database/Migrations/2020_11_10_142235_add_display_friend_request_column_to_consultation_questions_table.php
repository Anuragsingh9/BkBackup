<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class AddDisplayFriendRequestColumnToConsultationQuestionsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('consultation_questions', function (Blueprint $table) {
                //as front already worked on this so we need to name it in camelCase
                $table->tinyInteger('displayFriendRequest')->default(0);
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('consultation_questions', function (Blueprint $table) {
                $table->dropColumn('displayFriendRequest');
            });
        }
    }
