<?php
/**
 * Created by PhpStorm.
 * User: Sourabh Pancharia
 * Date: 6/24/2019
 * Time: 12:14 PM
 */

namespace Modules\Crm\Services;

use App\Entity;
use App\User;
use Auth;
use Modules\Crm\Entities\Contact;
use Modules\Crm\Entities\CrmNote;
use App\WorkshopMeta;
use Carbon\Carbon;

class FilterResultService
{
    /**
     * SuperAdminSingleton constructor.
     */
    // private $contactServices;


    /**
     * Make instance of SuperAdmin singleton class
     * @return SuperAdmin|null
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    public function addNewMembers(array $persons, int $workshop)
    {
        try {
            $users = User::whereIn('id', collect($persons)->where('type', 'user')->pluck('user_id'))->get(['id', 'fname']);
            $contacts = Contact::whereIn('id', collect($persons)->where('type', 'contact')->pluck('user_id'))->get(['id', 'fname']);

            return  $this->addWorkshopMembers($users, $workshop);
//            $this->addContactAsWorkshopMembers($contacts, $workshop);
        } catch (\Exception $e) {

        }

    }

    public function addWorkshopMembers($users, $workshop)
    {
        $workShopUsers = [];
        $users->map(function ($name, $key) use ($workshop, &$workShopUsers) {
            $workShopUsers[$key]['workshop_id'] = $workshop;
            $workShopUsers[$key]['user_id'] = $name->id;
            $workShopUsers[$key]['role'] = 'M2';
            $workShopUsers[$key]['created_at'] = Carbon::now()->format('Y-m-d');
            return $workShopUsers;
        });

        if (count($workShopUsers) > 0) {
            WorkshopMeta::insert($workShopUsers);
        }
        return count($workShopUsers);
    }

    public function addContactAsWorkshopMembers($contacts, $workshop)
    {
        $workShopUsers = [];
        $contacts->map(function ($name, $key) use ($workshop, &$workShopUsers) {
            $workShopUsers[$key]['workshop_id'] = $workshop;
            $workShopUsers[$key]['user_id'] = $name->id;
            $workShopUsers[$key]['role'] = 'M2';
            $workShopUsers[$key]['created_at'] = Carbon::now()->format('Y-m-d');
            return $workShopUsers;
        });

        if (count($workShopUsers) > 0) {
            WorkshopMeta::insert($workShopUsers);
        }
        return count($workShopUsers);
    }

}
