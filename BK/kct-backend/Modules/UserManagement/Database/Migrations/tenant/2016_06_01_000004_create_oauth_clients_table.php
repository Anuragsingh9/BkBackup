<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthClientsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('secret', 100)->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
        });
        $data = [
            [
                'id'                     => 1,
                'user_id'                => null,
                'name'                   => 'Keep Contact Personal Access Client',
                'secret'                 => 'oWCloUpDcNqgqdTUvad3iibmmWKGOPrSEEgjRgG4',
                'provider'               => null,
                'redirect'               => env("APP_URL"),
                'personal_access_client' => 1,
                'password_client'        => 0,
                'revoked'                => 0,
                'created_at'             => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at'             => \Carbon\Carbon::now()->toDateTimeString(),
            ],
            [
                'id'                     => 2,
                'user_id'                => NULL,
                'name'                   => 'Keep Contact Password Grant Client',
                'secret'                 => 'vJayJ4aq9GwytJ6TRRUSRSxiBRvyAHvl3L5nNWt8',
                'provider'               => 'users',
                'redirect'               => env("APP_URL"),
                'personal_access_client' => 0,
                'password_client'        => 0,
                'revoked'                => 0,
                'created_at'             => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at'             => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        ];
        \Illuminate\Support\Facades\DB::table('oauth_clients')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('oauth_clients');
    }
}
