<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->integer('level');
            $table->integer('parent');
            $table->timestamps();
           // $table->index('level');
           // $table->index('parent');
        });
        $data = [['id' => 1, 'level' => 1, 'parent' => '0', 'name' => 'Instances de lobbying'],
            ['id' => 2, 'level' => 1, 'parent' => '0', 'name' => 'Companies']
        ];

        DB::table('entity_types')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_types');
    }
}
