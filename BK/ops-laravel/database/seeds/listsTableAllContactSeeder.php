<?php

use Illuminate\Database\Seeder;
use App\Model\ListModel;
use Modules\Newsletter\Entities\IcontactMeta;
use Modules\Newsletter\Services\IContactSingleton;
class listsTableAllContactSeeder extends Seeder
{
    public function __construct()
    {
        $this->iContact = IContactSingleton::getInstance();
      }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $typoid=DB::connection('tenant')->table('newsletter_typology')->where(['name' => 'NewsletterList'])->first();
//        $data=[[
//            'name' => 'All User',
//            'description' => 'All User',
//            'type' => '3',
//            'typology_id'=>$typoid->id
//        ],
//        [
//            'name' => 'All Contact',
//            'description' => 'All Contact',
//            'type' => '4',
//            'typology_id'=>$typoid->id
//        ]
//        ];
//        $postData=[[
//            'name' => 'All User',
//        ],
//        [
//            'name' => 'All Contact',
//        ]
//        ];
//        $list=$this->iContact->addList($postData);
//        if($typoid){
//            foreach ($data as $key => $value) {
//                $listModel= ListModel::updateOrCreate(['name'=>$value['name']],$value);
//                IcontactMeta::updateOrCreate(['column_id' => $listModel->id],['column_id' => $listModel->id, 'icontact_id' => $list->lists[$key]->listId, 'type' => 2]);
//            }
//        }
    }
}
