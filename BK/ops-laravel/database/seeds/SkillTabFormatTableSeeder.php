<?php

use App\Model\SkillTabFormat;
use Illuminate\Database\Seeder;

class SkillTabFormatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*, [
                'name_en' => 'Comment Text',
                'name_fr' => 'Pos_Company',
            ], [
                'name_en' => 'Address',
                'name_fr' => 'Pos_Company',
            ]*/
        $data = [     [
            'id' => 1,
            'name_en' => 'Checkbox',
            'name_fr' => 'Case à cocher',
            'short_name' => 'checkbox_input',
            'field_type' => 'boolean'
        ],
            [
                'id' => 2,
                'name_en' => 'yes/no',
                'name_fr' => 'Oui/Non',
                'short_name' => 'yes_no_input',
                'field_type' => 'boolean'
            ],
            [
                'id' => 3,
                'name_en' => 'Percentage 0-100',
                'name_fr' => 'Pourcentage',
                'short_name' => 'percentage_input',
                'field_type' => 'range'
            ],
            [
                'id' => 4,
                'name_en' => 'Scale 1-10',
                'name_fr' => 'Curseur de 1 à 10',
                'short_name' => 'scale_10_input',
                'field_type' => 'range'
            ],
            [
                'id' =>5,
                'name_en' => 'Scale 1-5',
                'name_fr' => 'Curseur de 1 à 5',
                'short_name' => 'scale_5_input',
                'field_type' => 'range'
            ],
            [
                'id' => 6,
                'name_en' => 'Text',
                'name_fr' => 'Texte',
                'short_name' => 'text_input',
                'field_type' => 'text'
            ],
            [
                'id' => 7,
                'name_en' => 'Files',
                'name_fr' => 'Fichier',
                'short_name' => 'file_input',
                'field_type' => 'file'
            ],
            [
                'id' => 8,
                'name_en' => 'Select',
                'name_fr' => 'Liste déroulante',
                'short_name' => 'select_input',
                'field_type' => 'select'
            ],
            [
                'id' => 9,
                'name_en' => 'Numerical',
                'name_fr' => 'Numerique',
                'short_name' => 'numerical_input',
                'field_type' => 'numerical'
            ],
            [
                'id' => 10,
                'name_en' => 'Long Text',
                'name_fr' => 'Texte long',
                'short_name' => 'long_text_input',
                'field_type' => 'long_text'
            ],
            [
                'id' => 11,
                'name_en' => 'Date',
                'name_fr' => 'Date',
                'short_name' => 'date_input',
                'field_type' => 'date'
            ],
            [
                'id' => 12,
                'name_en' => 'Mandatory Checkbox',
                'name_fr' => 'Case à cocher obligatoire',
                'short_name' => 'mandatory_checkbox_input',
                'field_type' => 'boolean'
            ],
            [
                'id' => 13,
                'name_en' => 'Mandatory File',
                'name_fr' => 'Fichier obligatoire',
                'short_name' => 'mandatory_file_input',
                'field_type' => 'file'
            ],
            [
                'id' => 14,
                'name_en' => 'Comment',
                'name_fr' => 'Commentaire',
                'short_name' => 'comment_text_input',
                'field_type' => 'text'
            ],
            [
                'id' => 15,
                'name_en' => 'Address',
                'name_fr' => 'Adresse',
                'short_name' => 'address_text_input',
                'field_type' => 'long_text'
            ],
            [
                'id' => 16,
                'name_en' => 'Blank line',
                'name_fr' => 'Ligne blanche',
                'short_name' => 'blank_line',
                'field_type' => 'blank_line'
            ],
            [
                'id' => 17,
                'name_en' => 'Mandatory acceptance',
                'name_fr' => 'Acceptation obligatoire',
                'short_name' => 'mandatory_acceptance_input',
                'field_type' => 'boolean'
            ], [
                'id' => 18,
                'name_en' => 'Conditional CheckBox',
                'name_fr' => 'Case à cocher conditionnelle',
                'short_name' => 'conditional_checkbox_input',
                'field_type' => 'boolean'
            ], [
                'id' => 19,
                'name_en' => 'Radio Button',
                'name_fr' => 'Radio Button',
                'short_name' => 'radio_input',
                'field_type' => 'radio'
            ], [
                'id' => 20,
                'name_en' => 'Referrer',
                'name_fr' => 'Référent',
                'short_name' => 'referrer_input',
                'field_type' => ''
            ], [
                'id' => 21,
                'name_en' => 'File for certificate',
                'name_fr' => 'Fichier pour attestation',
                'short_name' => 'file_input',
                'field_type' => 'file'
            ], [
                'id' => 22,
                'name_en' => 'File for memo',
                'name_fr' => 'Fichier pour mémo',
                'short_name' => 'file_input',
                'field_type' => 'file'
            ], [
                'id' => 23,
                'name_en' => 'Siret',
                'name_fr' => 'Siret',
                'short_name' => 'text_input',
                'field_type' => 'text'
            ]

        ];
        foreach ($data as $key => $value) {
            SkillTabFormat::updateOrCreate(['name_en' => $value['name_en']], $value);
        }
    }
}
