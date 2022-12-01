<?php

use Illuminate\Database\Seeder;
use App\Model\ListModel;
use App\User;
use Modules\Newsletter\Entities\IcontactMeta;
use Modules\Newsletter\Services\IContactSingleton;
class defaultListSeeder extends Seeder
{
    public function __construct()
    {
//        $this->iContact = IContactSingleton::getInstance();
      }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $typoid=DB::connection('tenant')->table('newsletter_typology')->where(['name' => 'NewsletterList'])->first();
//        $data=[
//            'name' => 'default list',
//            'description' => 'default-list',
//            'type' => '2',
//            'typology_id'=>$typoid->id
//
//        ];
//        $postData=[[
//            'name' => 'default list',
//        ]
//        ];
//        $list=$this->iContact->addList($postData);
//
//            $listModel= ListModel::updateOrCreate(['name'=>$data['name']],$data);
//            IcontactMeta::create(['column_id' => $listModel->id, 'icontact_id' => $list->lists[0]->listId, 'type' => 2]);
//            $user=User::where('role','M1')->first();
//            $icontactContact=$this->iContact->createContact([[
//                'email'=>$user->email,
//                'firstName'=>$user->fname,
//                'lastName'=>$user->lname,
//                'ops_id'=>$user->id
//            ]]);
//            if($icontactContact->contacts){
//                foreach($icontactContact->contacts as $k=>$data){
//                    $metaC=[
//                        'column_id'=>$user->id,
//                        'icontact_id'=>$data->contactId,
//                        'type'=>6
//                    ];
//                }
//                $IcontactMeta=IcontactMeta::updateOrCreate(['column_id'=>$user->id,],$metaC);
//                $this->iContact->addSubscriber([[
//                          "listId"=>$list->lists[0]->listId,
//                          "contactId"=>$IcontactMeta->icontact_id,
//                          "status"=>"normal"
//                        ]]);
//        }
    }
}
