<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('long_name',100)->nullable();
            $table->string('short_name',100)->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('zip_code',10)->nullable();
            $table->string('city',100)->nullable();
            $table->string('country',100)->nullable();
            $table->string('phone',15)->nullable();
            $table->string('email',100)->nullable();
            $table->integer('entity_type_id');
            $table->integer('created_by');
            $table->integer('is_active')->default(1);
            $table->timestamps();
            //$table->index('entity_type_id');
            //$table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities');
    }
}
