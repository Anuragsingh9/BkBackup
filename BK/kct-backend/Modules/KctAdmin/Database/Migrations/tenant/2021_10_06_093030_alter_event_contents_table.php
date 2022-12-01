<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEventContentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('event_contents', function (Blueprint $table) {
            $table->unsignedBigInteger('content_type_id')->change();
            $table->renameColumn('content_type_id', 'id');
            $table->unsignedBigInteger('moment_type')->comment('(1.networking), (2.default_zoom_webinar), (3.zoom_webinar), (4.zoom_meeting), (5.youtube_pre_recorded), (6.vimeo_pre_recorded)')->after('end_time');
            $table->foreign('moment_type')->references('id')->on('event_moment_types')->cascadeOnDelete();
            $table->renameColumn('content_id', 'moment_id');
            $table->renameColumn('content_settings', 'moment_settings');
            $table->string('moment_name')->after('content_settings');
            $table->string('moment_description')->after('moment_name');
        });
        Schema::rename('event_contents', 'event_moments');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('event_moments', function (Blueprint $table) {
            $table->dropForeign('event_contents_moment_type_foreign');
            $table->integer("id")->change();
            $table->renameColumn('id', 'content_type_id');
            $table->dropColumn('moment_type');
            $table->renameColumn('moment_id', 'content_id');
            $table->renameColumn('moment_settings', 'content_settings');
            $table->dropColumn('moment_name');
            $table->dropColumn('moment_description');
        });
        Schema::rename('event_moments', 'event_contents');
    }
}
