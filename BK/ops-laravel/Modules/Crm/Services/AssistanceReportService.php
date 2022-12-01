<?php
/**
 * Created by PhpStorm.
 * User: Sourabh Pancharia
 * Date: 3/30/2020
 * Time: 09:21 PM
 */

namespace Modules\Crm\Services;

use App\Entity;
use App\User;
use Auth;
use App\Model\Contact;
//use Modules\Crm\Entities\Contact;
use Modules\Crm\Entities\AssistanceReport;

class AssistanceReportService
{
    /**
     * SuperAdminSingleton constructor.
     */
    private $contactServices;

    public function __construct()
    {

        //$this->contactServices = NotesService::getInstance();
    }

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

    public function addUser()
    {

    }

    /**
     * @param $array
     * @return mixed
     *
     * TO generate the logs of creation of user, contact, or entity
     */
    public function addReport($array)
    {
        //checking type is User
        if ($array['type'] == 'user') {
            $field = User::find($array['field_id'], ['id']);
        } elseif ($array['type'] == 'contact') {
            //checking type is Contact
            $field = Contact::find($array['field_id'], ['id']);
        } elseif ($array['type'] == 'entity' || $array['type'] == 'instance' || $array['type'] == 'company' || $array['type'] == 'union' || $array['type'] == 'press') {
          
            //checking type is Entity
            //checking type is Entity
            $field = Entity::find($array['field_id'], ['id']);
        }
        return $note = $field->assistance_reports()->create([
            'reports' => $array['reports'],
            'created_by' => Auth::user()->id,
            'crm_assistance_type_id' => $array['crm_assistance_type_id'],
        ]);
    }

    public function editReport($array, $id)
    {
        return AssistanceReport::where('id', $id)->update([
            'reports' => $array['reports'],
        ]);
    }

    public function getReports($id, $type,$assId=0)
    {
        $permissions = \Auth::user()->permissions;
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?? 0;
        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?? 0;
        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?? 0;
        $with = ['assistance_reports' => function ($q) use ($crmAdmin, $crmEditor, $crmAssistance, $crmRecruitment,$assId) {
            $q->orderBy('created_at', 'desc');
            if ((!$crmAdmin)) {
                if ((!$crmEditor) || $crmAssistance || $crmRecruitment) {
//                    if ($crmAssistance || $crmRecruitment)
                    $q->where('created_by', Auth::user()->id);
                }
            }
            if ($assId>0) {
//                    if ($crmAssistance || $crmRecruitment)
                $q->where('crm_assistance_type_id', $assId);
            }
        }];

        //checking type is User
        if ($type == 'user') {
            $field = User::with($with)->find($id, ['id']);
        } elseif ($type == 'contact') {
            //checking type is Contact
            $field = Contact::with($with)->find($id, ['id']);
        } elseif ($type == ('company' || 'union' || 'instance')) {
            //checking type is Entity
            $field = Entity::with($with)->find($id, ['id']);
        }

        return $field;
    }
}
