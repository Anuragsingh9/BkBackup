<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class CreateCrmLastOpenFilters extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('crm_user_open_filters', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->index();
                $table->unsignedInteger('filter_type_id')->index();
                $table->unsignedInteger('filter_id')->index()->nullable();
                
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('filter_type_id')->references('id')->on('crm_filter_types')
                    ->onDelete('cascade');
                $table->foreign('filter_id')
                    ->references('id')->on('crm_filters')->onDelete('SET NULL')->onUpdate('SET NULL');
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('crm_user_open_filters');
        }
    }
