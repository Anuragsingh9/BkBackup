<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthPersonalAccessClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->index();
            $table->timestamps();
        });
        $data=[['id'=>1, 'client_id'=>1, 'created_at'=>'2018-03-14 05:25:36', 'updated_at'=>'2018-03-14 05:25:36'],
['id'=>2, 'client_id'=>2, 'created_at'=>'2018-03-14 05:25:55', 'updated_at'=>'2018-03-14 05:25:55']];

DB::table('oauth_personal_access_clients')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_personal_access_clients');
    }
}
