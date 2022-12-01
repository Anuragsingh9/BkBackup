<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToSkillTabs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //        Schema::table('skill_tabs', function (Blueprint $table) {
        //            $table->tinyInteger('tab_type')
        //                ->comment('(0=>Users), (1=>Contact), (2=>Company), (3=>Instance), (4=>Union), (5=>Candidate), (6=>Referent), (7=>Press)')
        //                ->change();
        //        });
//        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `skill_tabs` CHANGE `tab_type` `tab_type` tinyint(4) COMMENT \'(0=>Users), (1=>Contact), (2=>Company), (3=>Instance), (4=>Union), (5=>Candidate), (6=>Referent), (7=>Press)\'');

    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

    }
}
