<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBanTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('kct_user_bans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('severity')->default(1);
            $table->text('ban_reason')->nullable();
            $table->string('ban_type')->nullable();
            $table->string("banable_type")->nullable()->comment("Model type of ban if any");
            $table->string('banable_id')->nullable()->comment("Id of the model on which user banned");
            $table->unsignedBigInteger("banned_by");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('banned_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('kct_user_bans');
    }
}
