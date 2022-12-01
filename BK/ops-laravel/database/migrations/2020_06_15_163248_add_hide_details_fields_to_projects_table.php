<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddHideDetailsFieldsToProjectsTable  extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('projects', function (Blueprint $table) {
                $table->text('project_description')->nullable();
                $table->string('project_goal',255)->nullable();
                $table->string('project_result',255)->nullable();
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('projects', function (Blueprint $table) {
            
            });
        }
    }
