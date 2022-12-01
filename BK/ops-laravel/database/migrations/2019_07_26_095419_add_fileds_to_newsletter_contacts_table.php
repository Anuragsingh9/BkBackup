<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFiledsToNewsletterContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletter_contacts', function (Blueprint $table) {
            Schema::table('newsletter_contacts', function (Blueprint $table) {
                $table->string('phone', 12)->nullable();
                $table->string('mobile', 12)->nullable();
                $table->text('address', 65535)->nullable();
                $table->string('postal', 8)->nullable();
                $table->string('city', 80)->nullable();
                $table->string('country', 80)->nullable();
            });
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    
    }
}
