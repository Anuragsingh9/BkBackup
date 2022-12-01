<?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class CreateConsultationSignupClassesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('consultation_signup_classes', function (Blueprint $table) {
                $table->uuid('uuid');
                $table->primary('uuid');
                $table->string('label');
                $table->tinyInteger('class_type')->default(1)->comment('1=union,2=company');
                $table->tinyInteger('class_for')->default(1)->comment('1=signup,2=sigin');
                $table->json('label_setting')->nullable();
                $table->integer('sort_order')->nullable()->default(0);
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
            Schema::dropIfExists('consultation_signup_classes');
        }
    }
