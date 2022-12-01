<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillCompanyView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
      CREATE OR REPLACE VIEW skill_company_view AS
      (
      SELECT
    `skill_data`.`skill_id` AS `skill_id`,
    `skill_data`.`name` AS `name`,
    `skill_data`.`tab_type` AS `tab_type`,
    `skill_data`.`skill_tab_id` AS `skill_tab_id`,
    `skill_data`.`short_name` AS `short_name`,
    `entities`.`id` AS `entity_id`,
    `entities`.`entity_type_id` AS `entity_type_id`,
    `entities`.`long_name` AS `long_name`,
    `entities`.`short_name` AS `entity_short_name`,
    `entities`.`address1` AS `address1`,
    `entities`.`zip_code` AS `zip_code`,
    `entities`.`city` AS `city`,
    `entities`.`country` AS `country`,
    `entities`.`phone` AS `phone`,
    `entities`.`email` AS `email`,
    `entities`.`fax` AS `fax`,
    `entities`.`entity_description` AS `entity_description`,
    `entities`.`entity_website` AS `entity_website`,
    `entities`.`industry_id` AS `industry_id`,
    `entities`.`entity_sub_type` AS `entity_sub_type`,
    COALESCE(
        (
        SELECT CASE
            `skill_data`.`short_name` WHEN 'date_input' THEN `user_skills`.`date_input` WHEN 'yes_no_input' THEN `user_skills`.`yes_no_input` WHEN 'checkbox_input' THEN `user_skills`.`checkbox_input` WHEN 'scale_10_input' THEN `user_skills`.`scale_10_input` WHEN 'scale_5_input' THEN `user_skills`.`scale_5_input` WHEN 'yes_no_input' THEN `user_skills`.`yes_no_input` WHEN 'percentage_input' THEN `user_skills`.`percentage_input` WHEN 'numerical_input' THEN `user_skills`.`numerical_input` WHEN 'text_input' THEN `user_skills`.`text_input` WHEN 'address_text_input' THEN `user_skills`.`address_text_input` WHEN 'long_text_input' THEN `user_skills`.`long_text_input` WHEN 'select_input' THEN `user_skills`.`select_input` WHEN 'radio_input' THEN `user_skills`.`select_input`
    END
FROM
    `user_skills`
WHERE
    `user_skills`.`field_id` = `entities`.`id` AND `user_skills`.`skill_id` = `skill_data`.`skill_id` AND `user_skills`.`type` = 'company'
GROUP BY
    `user_skills`.`field_id`
ORDER BY
    `user_skills`.`id`
DESC
LIMIT 1
    ),
    0
) AS `value`,(
    SELECT CASE
        `skill_data`.`short_name` WHEN 'date_input' THEN `user_skills`.`date_input` WHEN 'yes_no_input' THEN `user_skills`.`yes_no_input` WHEN 'checkbox_input' THEN `user_skills`.`checkbox_input` WHEN 'scale_10_input' THEN `user_skills`.`scale_10_input` WHEN 'scale_5_input' THEN `user_skills`.`scale_5_input` WHEN 'yes_no_input' THEN `user_skills`.`yes_no_input` WHEN 'percentage_input' THEN `user_skills`.`percentage_input` WHEN 'numerical_input' THEN `user_skills`.`numerical_input` WHEN 'text_input' THEN `user_skills`.`text_input` WHEN 'address_text_input' THEN `user_skills`.`address_text_input` WHEN 'long_text_input' THEN `user_skills`.`long_text_input` WHEN 'select_input' THEN `user_skills`.`select_input` WHEN 'radio_input' THEN `user_skills`.`select_input`
END
FROM
    `user_skills`
WHERE
    `user_skills`.`field_id` = `entities`.`id` AND `user_skills`.`skill_id` = `skill_data`.`skill_id` AND `user_skills`.`type` = 'company'
GROUP BY
    `user_skills`.`field_id`
ORDER BY
    `user_skills`.`id`
DESC
LIMIT 1
) AS `original_value`
FROM
    (
        `skill_data`
    JOIN `entities`
    )
WHERE
    `skill_data`.`tab_type` = 2 AND `entities`.`entity_type_id` = 2
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
        DB::statement("DROP VIEW skill_company_view");
    }
}
