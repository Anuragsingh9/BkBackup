<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTagsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_tags', function (Blueprint $table) {
            $table->id();
            $table->integer("tag_type")->comment('(1. Professional), (2. Personal)');
            $table->integer('status')->default(3)->comment('(1. Accepted), (2. Rejected), (3. Pending)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user_tags');
    }
}
