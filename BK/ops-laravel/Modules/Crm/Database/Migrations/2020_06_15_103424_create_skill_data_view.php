<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillDataView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
      CREATE OR REPLACE VIEW skill_data AS
      (
        SELECT
    `skills`.`id` AS `skill_id`,
    `skills`.`name` AS `name`,
    `skill_tabs`.`tab_type` AS `tab_type`,
    `skills`.`skill_tab_id` AS `skill_tab_id`,
    `skill_tab_formats`.`short_name` AS `short_name`
FROM
(
    (
    `skill_tabs`
        LEFT JOIN `skills` ON
    (
        `skill_tabs`.`id` = `skills`.`skill_tab_id`
    )
        )
    LEFT JOIN `skill_tab_formats` ON
    (
        `skills`.`skill_format_id` = `skill_tab_formats`.`id`
    )
    )
WHERE
    `skills`.`id` IS NOT NULL AND `skill_tab_formats`.`short_name` IN(
        'date_input',
        'yes_no_input',
        'checkbox_input',
        'scale_10_input',
        'scale_5_input',
        'yes_no_input',
        'percentage_input',
        'numerical_input',
        'text_input',
        'long_text_input',
        'address_text_input',
        'select_input',
        'radio_input'
    )
      )
    ");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW skill_data");
    }
}
