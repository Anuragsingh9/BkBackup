<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColoumnTraineeRoleToSubRoleInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sub_role', 5)->nullable()->comment('1=candidate');
        });
    }
    
   /* public function up()
    {
//        if (Schema::hasColumn('users', 'trainee_role')){
//            Schema::table('users', function (Blueprint $table) {
//                $table->string('trainee_role',5)->default(null)->nullable()->comment('1=candidate')->change();
//            });
//            Schema::table('users', function (Blueprint $table) {
//               $table->renameColumn('trainee_role', 'sub_role');
//            });
//        }
//        else{
            Schema::table('users', function (Blueprint $table) {
            $table->string('sub_role', 5)->nullable()->comment('1=candidate');
            });
//        }
    }*/

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
