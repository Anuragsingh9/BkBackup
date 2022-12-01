<?php

namespace Modules\Crm\Database\Seeders;
use App\Entity;
use App\Model\Skill;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Contact;
use Modules\Crm\Entities\CrmFilterType;

class SeedCrmFilterTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
       // DB::table('crm_filter_types')->delete();
        
        $data = [
            [
                'id' =>1,
                'identifier' => 'user',
                'name' => 'Person',
                'fr_name' => 'Personne',
                'component' => json_encode(
                    [
                        'company' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'union' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'instance' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'press' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'persons' => ['default' => [User::class, Contact::class], 'custom' => [Skill::class]],
                        'user' => ['default' => [User::class], 'custom' => [Skill::class]],
                        'contact' => ['default' => [Contact::class], 'custom' => [Skill::class]],
                    ]
                )
            ],
            [
                'id' =>2,
                'identifier' => 'entity',
                'name' => 'Company',
                'fr_name' => 'Société',
                'component' => json_encode(
                    [
                        'company' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'persons' => ['default' => [User::class, Contact::class], 'custom' => [Skill::class]],
                        'user' => ['default' => [User::class], 'custom' => [Skill::class]],
                        'contact' => ['default' => [Contact::class], 'custom' => [Skill::class]],
                    ]
                )
            ],
            [
                'id' =>3,
                'identifier' => 'entity',
                'name' => 'Union',
                'fr_name' => 'Syndicat',
                'component' => json_encode(
                    [
                        'union' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'persons' => ['default' => [User::class, Contact::class], 'custom' => [Skill::class]],
                        'user' => ['default' => [User::class], 'custom' => [Skill::class]],
                        'contact' => ['default' => [Contact::class], 'custom' => [Skill::class]],
                    ]
                )
            ],
            [
                'id' =>4,
                'identifier' => 'entity',
                'name' => 'Instance',
                'fr_name' => 'Instance',
                'component' => json_encode(
                    [
                        'instance' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'persons' => ['default' => [User::class, Contact::class], 'custom' => [Skill::class]],
                        'user' => ['default' => [User::class], 'custom' => [Skill::class]],
                        'contact' => ['default' => [Contact::class], 'custom' => [Skill::class]],
                    ]
                )
            ] ,[
                'id' =>5,
                'identifier' => 'entity',
                'name' => 'Press',
                'fr_name' => 'presse',
                'component' => json_encode(
                    [
                        'press' => ['default' => [Entity::class], 'custom' => [Skill::class]],
                        'persons' => ['default' => [User::class, Contact::class], 'custom' => [Skill::class]],
                        'user' => ['default' => [User::class], 'custom' => [Skill::class]],
                        'contact' => ['default' => [Contact::class], 'custom' => [Skill::class]],
                    ]
                )
            ],
        ];
        foreach ($data as $key => $value) {
            CrmFilterType::updateOrCreate(['id' => $value['id']], $value);
        }
     //   DB::table('crm_filter_types')->insert($data);
    }
}
