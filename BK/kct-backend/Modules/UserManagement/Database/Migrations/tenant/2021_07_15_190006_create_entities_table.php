<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_type_id');
            $table->string('long_name', 100)->nullable();
            $table->string('short_name', 100)->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('email', 500)->nullable();
            $table->integer('is_active')->default(1);
            $table->text('entity_description')->nullable();
            $table->string('entity_logo')->nullable();
            $table->string('entity_website')->nullable();
            $table->integer('industry_id')->nullable();
            $table->string('fax')->nullable();
            $table->integer('entity_ref_type')->default(0);
            $table->integer('is_internal')->default(0);
            $table->integer('entity_sub_type')->nullable()->comment('For Union, (1. Internal), (2, External)');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('entities');
    }
}
