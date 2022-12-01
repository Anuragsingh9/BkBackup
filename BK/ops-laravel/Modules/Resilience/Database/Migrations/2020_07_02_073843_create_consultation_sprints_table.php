<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class CreateConsultationSprintsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('consultation_sprints', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('consultation_uuid')->index();
                $table->string('title');
                $table->text('description_1')->nullable();
                $table->text('description_2')->nullable();
                $table->text('description_3')->nullable();
                $table->string('image_non_selected')->nullable();
                $table->string('image_selected')->nullable();
                $table->tinyInteger('is_accessible')->default(0);
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('consultation_uuid')->references('uuid')->on('consultations')->onDelete('cascade');
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('consultation_sprints');
        }
    }
