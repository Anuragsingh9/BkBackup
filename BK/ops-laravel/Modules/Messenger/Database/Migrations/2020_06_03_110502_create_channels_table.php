<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('im_channels', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->primary('uuid');
            $table->string('channel_name')->nullable(); // in case personal chat no need
            $table->tinyInteger('channel_type')->comment('(1-Workshop),(2-Channel),(3-Personal)');
            $table->tinyInteger('is_private')->default(0);
            $table->unsignedInteger('owner_id'); // in case personal chat no need
            $table->json('channel_fields')->nullable();
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
        Schema::dropIfExists('im_channels');
    }
}
