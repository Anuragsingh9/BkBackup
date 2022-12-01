<?php

    namespace Modules\Resilience\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Modules\Resilience\Entities\ConsultationQuestionType;

    class ConsultationQuestionTypesTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $data = [
                [
                    "question_type"         => "open_text",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "yes_no",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "single_option_text",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 1,
                    "format"                => '[{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0}]',
                ],
                [
                    "question_type"         => "multiple_option_text",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 1,
                    "format"                => '[{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0}]',
                ],
                [
                    "question_type"         => "trends",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "numeric",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "percentage",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "file",
                    "is_enable"             => 0,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "numerical_1_5_slider",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "numerical_1_10_slider",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "numerical_1_100_slider",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "date",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "single_option_with_1_10_slider",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 1,
                    "format"                => '[{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0}',
                ],
                [
                    "question_type"         => "multiple_option_with_1_10_slider",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 1,
                    "format"                => '[{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0}]',
                ],
                [
                    "question_type"         => "drag_drop_ranking",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 1,
                    "format"                => '[{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0}]',
                ],
                [
                    "question_type"         => "column_fields",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => '{"row": [{"label" : "row label"},{"label" : "row label"},{"label" : "row label"}],"column": [{"label" : "column label"},{"label" : "column label"},{"label" : "column label"}]',
                ],
                [
                    "question_type"         => "comment",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => 'null',
                ],
                [
                    "question_type"         => "single_option_text_comment",
                    "is_enable"             => 1,
                    "show_add_allow_button" => 0,
                    "format"                => '[{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0},{"label" : "option label","is_manual" : 0}]',
                ],
            ];


            //ConsultationQuestionType::truncate();
            foreach ($data as $key => $value) {
                ConsultationQuestionType::updateOrCreate(['question_type' => $value['question_type']], $value);
            }
        }
    }
