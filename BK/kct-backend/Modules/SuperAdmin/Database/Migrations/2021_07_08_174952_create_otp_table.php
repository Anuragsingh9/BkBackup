<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtpTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('otp_type')->default(1)->comment('1. Email, 2. Password Reset');
            $table->string('email', 500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('otp_codes');
    }
}
