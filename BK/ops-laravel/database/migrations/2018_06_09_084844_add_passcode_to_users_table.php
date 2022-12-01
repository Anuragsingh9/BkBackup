<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPasscodeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if (!Schema::hasColumn('users', 'login_code')){
            Schema::table('users', function (Blueprint $table) {
                $table->string('login_code', 20)->nullable();
            });
        } 
        if(!Schema::hasColumn('users', 'hash_code')){
                Schema::table('users', function (Blueprint $table) {
                    $table->string('hash_code', 20)->nullable();
                });
        }
        if(!Schema::hasColumn('users', 'on_off')){
                Schema::table('users', function (Blueprint $table) {
                     $table->tinyInteger('on_off')->default(1);
                });
        }
      
        
    }

    public function down(){
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
