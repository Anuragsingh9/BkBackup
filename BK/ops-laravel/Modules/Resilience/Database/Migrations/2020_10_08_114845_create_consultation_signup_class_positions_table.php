<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class CreateConsultationSignupClassPositionsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('consultation_signup_class_positions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('consultation_sign_up_class_uuid')->index('consultation_signup_class_uuid_index');
                $table->string('positions');
                $table->integer('sort_order')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('consultation_sign_up_class_uuid', 'consultation_signup_class_uuid_foreign')->references('uuid')->on('consultation_signup_classes')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('consultation_signup_class_positions');
        }
    }
