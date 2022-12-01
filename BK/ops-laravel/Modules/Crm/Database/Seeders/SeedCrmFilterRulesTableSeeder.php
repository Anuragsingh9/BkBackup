<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Crm\Entities\CrmFilterRule;
use Modules\Crm\Entities\CrmFilterType;

class SeedCrmFilterRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
       // DB::table('crm_filter_rules')->delete();
        $data = [
            [
                'id' => 1,
                'name' => 'Is',
                    'fr_name' => 'Est égal à',
                'short_name' => 'is',
                'operator' => '=',
                'value' => ':value',
                'available_formats' => json_encode([
                    "boolean","text","numerical","long_text","date","select","radio","range"
                ])
            ],[
                'id' => 2,
                'name' => 'Is not',
                    'fr_name' => 'N’est pas égal à',
                'short_name' => 'is_not',
                'operator' => '!=',
                'value' => ':value',
                'available_formats' => json_encode([
                    "boolean","text","numerical","long_text","date","select","radio","range"
                ])
            ],[
                'id' => 3,
                'name' => 'Is empty',
                    'fr_name' => 'Est vide',
                'short_name' => 'is_empty',
                'operator' => '=',
                'value' => ':value',
                'available_formats' => json_encode([
                    "boolean","text","numerical","long_text","date","select","radio","range"
                ])
            ],[
                'id' => 4,
                'name' => 'Is not empty',
                    'fr_name' => 'N’est pas vide',
                'short_name' => 'is_not_empty',
                'operator' => '!=',
                'value' => ':value',
                'available_formats' => json_encode([
                    "boolean","text","numerical","long_text","date","select","radio","range"
                ])
            ],[
                'id' => 5,
                'name' => 'Contains',
                    'fr_name' => 'Contient',
                'short_name' => 'contains',
                'operator' => 'LIKE',
                'value' => '%:value%',
                'available_formats' => json_encode([
                    'text',
                    'long_text',
                ])
            ],[
                'id' => 6,
                'name' => 'Does not contain',
                    'fr_name' => 'Ne contient pas',
                'short_name' => 'does_not_contain',
                'operator' => 'NOT LIKE',
                'value' => '%:value%',
                'available_formats' => json_encode([
                    'text',
                    'long_text',
                ])
            ],[
                'id' => 7,
                'name' => 'Starts with',
                    'fr_name' => 'Commence par',
                'short_name' => 'starts_with',
                'operator' => 'LIKE',
                'value' => ':value%',
                'available_formats' => json_encode([
                    'text',
                    'long_text',
                ])
            ],[
                'id' => 8,
                'name' => 'Does not start with',
                    'fr_name' => 'Ne commence pas par',
                'short_name' => 'does_not_start_with',
                'operator' => 'NOT LIKE',
                'value' => ':value%',
                'available_formats' => json_encode([
                    'text',
                    'long_text',
                ])
            ],[
                'id' => 9,
                'name' => 'Ends with',
                    'fr_name' => 'Se termine par',
                'short_name' => 'ends_with',
                'operator' => 'LIKE',
                'value' => '%:value',
                'available_formats' => json_encode([
                    'text',
                    'long_text',
                ])
            ],[
                'id' => 10,
                'name' => 'Does not end with',
                    'fr_name' => 'Ne se termine pas par',
                'short_name' => 'does_not_end_with',
                'operator' => 'NOT LIKE',
                'value' => '%:value',
                'available_formats' => json_encode([
                    'text',
                    'long_text',
                ])
            ],[
                'id' => 11,
                'name' => 'Greater Than',
                    'fr_name' => 'Plus grand que',
                'short_name' => 'greater_than',
                'operator' => '>',
                'value' => ':value',
                'available_formats' => json_encode([
                    'numerical',
                    'range',"date"
                ])
            ],[
                'id' => 12,
                'name' => 'Less Than',
                    'fr_name' => 'Moins que',
                'short_name' => 'less_than',
                'operator' => '<',
                'value' => ':value',
                'available_formats' => json_encode([
                    'numerical',
                    'range',"date"
                ])
            ],[
                'id' => 13,
                    'name' => 'Greater Than Equal To',
                    'fr_name' => 'Supérieur à égal à',
                'short_name' => 'greater_than_equals',
                'operator' => '>=',
                'value' => ':value',
                'available_formats' => json_encode([
                    'numerical',
                    'range',"date"
                ])
            ],[
                'id' => 14,
                    'name' => 'Less Than Equal To',
                    'fr_name' => 'Inférieur à égal à',
                'short_name' => 'less_than_equals',
                'operator' => '<=',
                'value' => ':value',
                'available_formats' => json_encode([
                    'numerical',
                    'range',"date"
                ])
            ]

        ];
        foreach ($data as $key => $value) {
            CrmFilterRule::updateOrCreate(['id' => $value['id']], $value);
        }
      //  DB::table('crm_filter_rules')->insert($data);
    }
}
