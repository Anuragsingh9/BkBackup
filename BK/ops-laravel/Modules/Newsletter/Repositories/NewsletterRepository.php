<?php

namespace Modules\Newsletter\Repositories;

use App\Grdp;
use App\Guest;
use App\MessageCategory;
use App\Milestone;
use App\Workshop;
use App\WorkshopCode;
use App\WorkshopMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Model\Newsletter;
use Modules\Newsletter\Repositories\NewsletterInterface as NewsletterInterface;
use DB;
use Auth,Session;
use App\Project;
use iContact\iContactApi;

class NewsletterRepository implements NewsletterInterface
{
    public function addNewSubscriber(){
        // todo: Do code to save data in DB
        $oiContact = $this->iContactApi();
        //$data = $oiContact->getMessages();
        //$data = $oiContact->getCampaigns();
        // $data = $oiContact->addContact('joee@shmoe.com', null, null, 'Joe', 'Shmoe', null, '123 Somewhere Ln', 'Apt 12', 'Somewhere', 'NW', '12345', '123-456-7890', '123-456-7890', null);
        //$data = $oiContact->addMessage('An Example Message', 9597, '<h1>An Example Message</h1>', 'An Example Message', 'ExampleMessage', 14887, 'normal');


        $data = $oiContact->addList('somelist', null, true, false, false, 'Just an example list', 'Some List');
        //$data = $oiContact->sendMessage(array(2), 25042, null, null, null, mktime(0,1,0));
        var_dump($data);
    }

    public function createCommission()
    {
        $acc_id = 1;
        $newRec = array();
        $project = array();
        $milestone = array();

        //Check if MainOrgAdmin already created workshop and project
        $wid = DB::connection('mysql')->table('workshops')->where('president_id',1)->where('code1','NSL')->first(['id']);
        if(!empty($wid)){
            return response()->json(['msg' => 'MainOrgAdmin already created workshop and project']);
        }

        // If not created workshop and project then create it for first time

        if(session()->has('superadmin')) {
            $superAdminPermission = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first(['newsletter_menu_enable']);
            if($superAdminPermission->newsletter_menu_enable == 1) {
                $workshop = array('president_id' => 1, 'validator_id' => 1, 'workshop_name' => 'Newsletter', 'workshop_desc' => '', 'code1' => 'NSL', 'code2' => '', 'workshop_type' => 1, 'is_private' => 0);
                $id = Workshop::insertGetId($workshop);
                if ($id) {
                    WorkshopCode::insert(['workshop_id' => $id, 'code' => 'NSL']);
                    $workshop_meta[0] = array('workshop_id' => $id, 'role' => '1', 'user_id' => 1);
                    $workshop_meta[1] = array('workshop_id' => $id, 'role' => '2', 'user_id' => 1);
                    $newRec = WorkshopMeta::insert($workshop_meta);

                    //add first message category under new created workshop
                    $msg=MessageCategory::insert(['category_name' => 'General', 'workshop_id' => $id, 'status' => 1]);

                    // todo: Check if newsletter_project_template is available then only create project automatically otherwise not
                $project = Project::create(['project_label' => 'Next Newsletter',
                    'user_id' => Auth::user()->id,
                    'wid' => $id,
                    'color_id' => 1,
                    'is_default_project' => 1,
                    'end_date' => '2099-12-31 00:00:00'
                ]);
                $milestone = Milestone::create(['project_id' => $project->id,
                    'label' => 'Étape Bazar',
                    'user_id' => Auth::user()->id,
                    'end_date' => '2099-12-31 00:00:00',
                    'color_id' => 1,
                    'start_date' => Carbon::now(),
                    'is_default_milestone' => 1
                ]);
            }
            }
        }
        return response()->json(['new_rec' => $newRec,'project' => $project,'milestone' => $milestone]);

    }



}
