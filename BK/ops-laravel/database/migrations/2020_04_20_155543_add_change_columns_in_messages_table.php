<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddChangeColumnsInMessagesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::disableForeignKeyConstraints();
            Schema::table('messages', function (Blueprint $table) {
                $table->integer('workshop_id')->index()->nullable()->change();
                $table->unsignedInteger('category_id')->index()->nullable()->change();
                $table->integer('user_id', FALSE, TRUE)->index()->comment('here UserId==from_id')->change();
                $table->integer('to_id', FALSE, TRUE)->index();
                $table->ipAddress('visitor_ip')->nullable();
                $table->softDeletes();
                $table->tinyInteger('type')->default(1)->comment('1 for message 2 for Personal Message');
//                $table->foreign('workshop_id')->references('workshops')->on('id');
//                $table->foreign('category_id')->references('message_categories')->on('id');
               // $table->foreign('from_id')->references('users')->on('id');
               // $table->foreign('to_id')->references('users')->on('id');
    
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('messages', function (Blueprint $table) {
                //
            });
        }
    }
