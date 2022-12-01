<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddColumnToPresenseTabble extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('presences', function (Blueprint $table) {
                // this is string because we can use it in any string or code
                $table->string('video_presence_status',10)->default(0);
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('presences', function (Blueprint $table) {
                //
            });
        }
    }
