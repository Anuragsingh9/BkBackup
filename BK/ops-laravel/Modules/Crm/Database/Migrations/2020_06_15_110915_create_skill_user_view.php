<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillUserView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
      CREATE OR REPLACE VIEW skill_user_view AS
      (
     SELECT
    `skill_data`.`skill_id` AS `skill_id`,
    `skill_data`.`name` AS `name`,
    `skill_data`.`tab_type` AS `tab_type`,
    `skill_data`.`skill_tab_id` AS `skill_tab_id`,
    `skill_data`.`short_name` AS `short_name`,
    `users`.`id` AS `user_id`,
    `users`.`fname` AS `fname`,
    `users`.`lname` AS `lname`,
    `users`.`email` AS `email`,
    `users`.`phone` AS `phone`,
    `users`.`mobile` AS `mobile`,
    `users`.`address` AS `address`,
    `users`.`postal` AS `postal`,
    `users`.`city` AS `city`,
    `users`.`country` AS `country`,
    COALESCE(
        (
        SELECT CASE
            `skill_data`.`short_name` WHEN 'date_input' THEN `user_skills`.`date_input` WHEN 'yes_no_input' THEN `user_skills`.`yes_no_input` WHEN 'checkbox_input' THEN `user_skills`.`checkbox_input` WHEN 'scale_10_input' THEN `user_skills`.`scale_10_input` WHEN 'scale_5_input' THEN `user_skills`.`scale_5_input` WHEN 'yes_no_input' THEN `user_skills`.`yes_no_input` WHEN 'percentage_input' THEN `user_skills`.`percentage_input` WHEN 'numerical_input' THEN `user_skills`.`numerical_input` WHEN 'text_input' THEN `user_skills`.`text_input` WHEN 'address_text_input' THEN `user_skills`.`address_text_input` WHEN 'long_text_input' THEN `user_skills`.`long_text_input` WHEN 'select_input' THEN `user_skills`.`select_input` WHEN 'radio_input' THEN `user_skills`.`select_input`
    END
FROM
    `user_skills`
WHERE
    `user_skills`.`user_id` = `users`.`id` AND `user_skills`.`skill_id` = `skill_data`.`skill_id`
GROUP BY
    `user_skills`.`user_id`
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
    `user_skills`.`user_id` = `users`.`id` AND `user_skills`.`skill_id` = `skill_data`.`skill_id`
GROUP BY
    `user_skills`.`user_id`
ORDER BY
    `user_skills`.`id`
DESC
LIMIT 1
) AS `original_value`
FROM
    (
        `skill_data`
    JOIN `users`
    )
WHERE
    `skill_data`.`tab_type` = 0 AND `users`.`sub_role` IS NULL
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
        DB::statement("DROP VIEW skill_user_view");
    }
}
