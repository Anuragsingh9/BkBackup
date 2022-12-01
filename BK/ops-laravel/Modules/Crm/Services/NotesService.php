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
use App\Model\Contact;
//use Modules\Crm\Entities\Contact;
use Modules\Crm\Entities\CrmNote;


class NotesService
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

    public function addNote($array)
    {
        //checking type is User
        if ($array['type'] == 'user') {
            $field = User::find($array['field_id'], ['id']);
        } elseif ($array['type'] == 'contact') {
            //checking type is Contact
            $field = Contact::find($array['field_id'], ['id']);
        }elseif ($array['type'] == 'entity' || $array['type'] == 'instance' || $array['type'] == 'company' || $array['type'] == 'union' || $array['type'] == 'press') {
            
            //checking type is Entity
            $field = Entity::find($array['field_id'], ['id']);
        }
        return $note = $field->notes()->create([
            'notes' => $array['notes'],
            'created_by' => Auth::user()->id,
        ]);
    }

    public function editNote($array, $id)
    {
        return CrmNote::where('id', $id)->update([
            'notes' => $array['notes'],
        ]);
    }

    public function getNotes($id, $type)
    {

        $permissions = \Auth::user()->permissions;
          $orgAdmin = (\Auth::user()->role == 'M1' || \Auth::user()->role == 'M0');
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?? 0;
        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?? 0;
        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?? 0;

        $with = ['notes' => function ($q) use ($crmAdmin, $crmEditor, $crmAssistance, $crmRecruitment, $orgAdmin) {
            $q->orderBy('created_at', 'desc');
            if ((!$orgAdmin && !$crmAdmin )) {
                if ((!$crmEditor) || $crmAssistance || $crmRecruitment) {
//                    if ($crmAssistance || $crmRecruitment)
                        $q->where('created_by', Auth::user()->id);
                }
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
