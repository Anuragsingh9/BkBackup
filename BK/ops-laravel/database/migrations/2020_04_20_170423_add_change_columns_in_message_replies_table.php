<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddChangeColumnsInMessageRepliesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('message_replies', function (Blueprint $table) {
                $table->ipAddress('visitor_ip')->nullable();
                $table->unsignedInteger('user_id')->comment('here UserId==from_id')->change();
                $table->softDeletes();
                $table->tinyInteger('type')->default(1)->comment('1 for message 2 for Personal Message');
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('message_replies', function (Blueprint $table) {
                //
            });
        }
    }
