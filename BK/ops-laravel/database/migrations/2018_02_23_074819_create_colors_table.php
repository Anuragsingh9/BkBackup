<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
//         Schema::drop('colors');
        Schema::create('colors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 40);
            $table->timestamps();
        });
        $data = [
            ["code" => "#454546"],
            ["code" => "#8415ce"],
            ["code" => "#f4e32e"],
            ["code" => "#e89709"],
            ["code" => "#ff4e14"],
            ["code" => "#cc1210"],
            ["code" => "#a91733"],
            ["code" => "#c11c90"],
            ["code" => "#0063bb"],
            ["code" => "#0b95bd"],
            ["code" => "#059758"],
            ["code" => "#6fc71f"],
            ["code" => "#a2c528"],
            ["code" => "#d6e52d"],
        ];
        DB::table('colors')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('colors');
    }

}
