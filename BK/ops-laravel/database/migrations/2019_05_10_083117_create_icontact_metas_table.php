<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcontactMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter_icontact_metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('column_id')->unsigned()->index();
            $table->string('icontact_id',255);
            $table->integer('type')->comment('0->sender, 1->subscriber, 2->list, 3->newsletter, 4->schedule timing, 5->stats 6->user');
            $table->timestamps();
            $table->softDeletes(); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletters_icontact_metas');
    }
}
