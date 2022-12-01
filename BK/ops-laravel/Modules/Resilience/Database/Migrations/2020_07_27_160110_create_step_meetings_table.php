<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class CreateStepMeetingsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::disableForeignKeyConstraints();
            Schema::create('consultation_step_meetings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('meeting_id');
                $table->uuid('consultation_uuid')->index();
                $table->unsignedBigInteger('consultation_step_id');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['meeting_id','consultation_step_id']);
                $table->foreign('consultation_uuid')->references('uuid')->on('consultations')->onDelete('cascade');
                $table->foreign('consultation_step_id')->references('id')->on('consultation_steps')->onDelete('cascade');
                $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
                Schema::enableForeignKeyConstraints();
            });
           
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('consultation_step_meetings');
        }
    }
