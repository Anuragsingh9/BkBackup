<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->nullable();
            $table->string('name');
            $table->string('secret', 100);
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
        });
         $data=[['id'=>1, 'user_id'=>1, 'name'=>'1', 'secret'=>'z2puOFQVleoWCQt1p68Brlt79uVDfIjC9FtTlZh2', 'redirect'=>'https://opsimplify.com', 'personal_access_client'=>0, 'password_client'=>0, 'revoked'=>0, 'created_at'=>'2018-04-06 06:32:57', 'updated_at'=>'2018-04-06 06:32:57'],['id'=>2, 'user_id'=>NULL, 'name'=>'Laravel Personal Access Client', 'secret'=>'rKxzTqcOxQdYoiKpglahlvYsz2LMIiabsj6fNkqU', 'redirect'=>'http://localhost','personal_access_client'=> 1,'password_client'=> 0,'revoked'=> 0, 'created_at'=>'2018-04-06 12:22:28', 'updated_at'=>'2018-04-06 12:22:28']];
          DB::table('oauth_clients')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_clients');
    }
}
