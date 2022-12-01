<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddFieldsToCrmFiltersTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('crm_filters', function (Blueprint $table) {
                $table->tinyInteger('is_default')->default(0)->comment('this is to check filter is default or not,0= normal 1= Default Filter');
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('crm_filters', function (Blueprint $table) {
                $table->dropColumn('is_default');
            });
        }
    }
