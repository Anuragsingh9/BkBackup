<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class AddColumnInCrmFilterTypes extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::table('crm_filter_types', function (Blueprint $table) {
                $table->string('fr_name')->nullable();
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::table('crm_filter_types', function (Blueprint $table) {
                $table->string('fr_name')->nullable();
            });
        }
    }
