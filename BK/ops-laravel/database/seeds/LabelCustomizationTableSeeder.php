<?php

    use App\Organisation;
use Illuminate\Database\Seeder;
use App\Model\LabelCustomization;
class LabelCustomizationTableSeeder extends Seeder
{
        /**
         * LabelCustomizationTableSeeder constructor.
         */
        public $host_name = '';
        
        public function __construct()
        {
            $org = Organisation::first();
            $this->host_name = isset($org->acronym) ? ucfirst(strtolower($org->acronym)) : 'OP simplify';
        }
        
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[[
            'id'=>1,
            'name' => 'Syndicat',
            'default_en' => 'Family of industries',
            'default_fr' => 'Famille d\'industries',

        ], [
            'id'=>2,
            'name' => 'Société',
            'default_en' => 'Industries',
            'default_fr' => 'Industries',

        ], [
            'id'=>3,
            'name' => 'Union',
            'default_en' => 'Union',
            'default_fr' => 'Syndicat',
        ],[
            'id'=>4,
            'name' => 'Position',
            'default_en' => 'Position in the union',
            'default_fr' => 'Fonction dans le syndicat ',
        ],[
            'id'=>5,
            'name' => 'Company',
            'default_en' => 'Company',
                    'default_fr' => 'Société',
        ],[
            'id'=>6,
            'name' => 'Pos_Company',
            'default_en' => 'Position in the company',
            'default_fr' => 'Fonction dans la Société',
        ],[
            'id'=>7,
            'name' => 'Internal_id',
            'default_en' => 'Internal ID',
            'default_fr' => 'Numéro Interne',
        ],[
            'id'=>8,
            'name' => 'Main_menu',
            'default_en' => 'Committees and workshops',
            'default_fr' => 'Commissions et Groupes de travail',
        ],[
            'id'=>9,
            'name' => 'Committee_menu',
            'default_en' => 'Committees and workshops',
            'default_fr' => 'Commissions et groupes de travail',
        ],[
            'id'=>10,
            'name' => 'Dashboard_menu',
            'default_en' => 'Committee or workshop',
            'default_fr' => 'Commission ou GT',
        ],[
            'id'=>11,
            'name' => 'Active_committees',
            'default_en' => 'My active committees',
            'default_fr' => 'Mes commissions actives',
        ]
            ,[
                'id'=>12,
            'name' => 'Document_search',
            'default_en' => 'Committees',
            'default_fr' => 'Commissions',
        ],
            [
                'id'=>13,
                'name' => 'company_name',
                'default_en' => 'OP/SOCIETE',
                'default_fr' => 'OP/SOCIETE',
            ],
            [
                'id'=>14,
                'name' => 'membership',
                'default_en' => 'OP D\'APPARTENANCE',
                'default_fr' => 'OP D\'APPARTENANCE',
            ],
            [
                'id'=>15,
                'name' => 'contacts',
                'default_en' => 'Contacts',
                'default_fr' => 'Contacts',
            ],

            [
                'id'=>16,
                'name' => 'instances',
                'default_en' => 'Instances',
                'default_fr' => 'Instances',
            ],
            [
                'id'=>17,
                'name' => 'tabs',
                'default_en' => 'Tabs',
                'default_fr' => 'Onglets',
            ],
            [
                'id'=>18,
                'name' => 'fields',
                'default_en' => 'Fields',
                'default_fr' => 'Champs',
            ],
            [
                'id'=>19,
                'name' => 'crm_editor',
                'default_en' => 'CrmEditor',
                'default_fr' => 'CrmEditor',
            ],
            [
                'id'=>20,
                'name' => 'crm_dev_staff',
                'default_en' => 'CrmDevStaff',
                'default_fr' => 'CrmDevStaff',
            ],
            [
                'id'=>21,
                'name' => 'assistance_staff',
                'default_en' => 'Assistance staff',
                'default_fr' => 'Assistance staff',
            ],
            [
                'id'=>22,
                'name' => 'choose_workshop',
                'default_en' => 'Choose your Committee',
                'default_fr' => 'Choisissez votre Fédération',
            ],
           
        [
            'id'=>23,
                'name' => 'qualification',
                'default_en' => 'Qualification',
                'default_fr' => 'Carte TP Pro Artisan',
            ],
              [
                'id'=>24,
                'name' => 'enter_zipcode',
                'default_en' => 'Enter the first two figures of your federation zipcode',
                'default_fr' => 'Entrez les deux premiers chiffres du département de votre fédération',
            ], [
                'id'=>25,
                'name' => 'cards_being_created',
                'default_en' => 'Cards being created',
                'default_fr' => 'Cartes en cours de création ou de renouvellement!',
            ], [
                'id'=>26,
                'name' => 'waiting_validation',
                'default_en' => 'Waiting For Pre-Validator',
                'default_fr' => 'Cartes en attente de pré-validation',
            ], [
                'id'=>27,
                'name' => 'decision_pending',
                'default_en' => 'Cards With Decision Pending',
                'default_fr' => 'Cartes en attente de décision',
            ], [
                'id'=>28,
                'name' => 'validated_card',
                'default_en' => 'Validated Cards',
                'default_fr' => 'Cartes attribuées en cours de validité',
            ], [
                'id'=>29,
                'name' => 'archived_card',
                'default_en' => 'Archived Cards',
                'default_fr' => 'Cartes archivées',
            ], [
                'id'=>30,
                'name' => 'rejected_card',
                'default_en' => 'Rejected cards',
                'default_fr' => 'Cartes non validées',
            ], [
                'id'=>31,
                'name' => 'option_3',
                'default_en' => 'If you are new in the business, you can upload a technical memo',
                'default_fr' => 'Si vous êtes en création seulement, vous pouvez transmettre un mémoire technique',
            ], [
                'id'=>32,
                'name' => 'memo_technique',
                'default_en' => 'Technical memo',
                'default_fr' => 'Mémoire technique',
            ],[
                'id'=>33,
                'name' => 'card_opinion',
                'default_en' => 'Cards with opinions pending',
                'default_fr' => 'Cartes en attente d’avis',
            ],[
                'id'=>34,
                'name' => 'you_are_member',
                'default_en' => 'You are a member of',
                'default_fr' => 'Vous êtes adhérent de la',
                
                ], ['id'         => 35,
                    'name'       => 'default_welcome_account_name',
                    'default_en' => 'Welcome to ' . ucfirst($this->host_name),
                    'default_fr' => 'Bienvenue to ' . ucfirst($this->host_name),

                ], ['id'         => 36,
                    'name'       => 'eye_tiptool',
                    'default_en' => 'Help your candidate to fill-in his/her request',
                    'default_fr' => 'Aider votre adhérent à remplir sa demande',
                
                ], ['id'         => 37,
                    'name'       => 'hammer_tiptool',
                    'default_en' => 'Validate',
                    'default_fr' => 'Valider',
                
                ],['id'         => 39,
                    'name'       => 'display_qualification_files',
                    'default_en' => 'Fichier des non-inscrits',
                    'default_fr' => 'Reservoirs',
                
                ],
        ];
        foreach ($data as $key => $value) {
            LabelCustomization::updateOrCreate(['id'=>$value['id']],$value);
        }
    }
    
}
