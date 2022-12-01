<?php
/**
 * Created by PhpStorm.
 * User: Sourabh Pancharia
 * Date: 6/25/2019
 * Time: 04:14 PM
 */

namespace Modules\Crm\Services;

use App\Entity;
use App\Model\TaskComment;
use App\User;
use Auth;
use Modules\Crm\Entities\Contact;

class CommentService
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

    public function addComment($array, $wid)
    {
        //checking type is User
        if ($array['type'] == 'user') {
            $field = User::find($array['field_id'], ['id']);
        } elseif ($array['type'] == 'contact') {
            //checking type is Contact
            $field = Contact::find($array['field_id'], ['id']);
        } elseif ($array['type'] == ('company' || 'union' || 'instance')) {
            //checking type is Entity
            $field = Entity::find($array['field_id'], ['id']);
        }
        return $note = $field->comments()->create([
            'comment' => $array['comment'],
            'workshop_id' => $wid,
            'user_id' => Auth::user()->id,

        ]);
    }

    public function editComment($array, $id)
    {
        return TaskComment::where('id', $id)->update([
            'comment' => $array->comment,
        ]);
    }

    public function getComments($id, $type)
    {
        $permissions = \Auth::user()->permissions;
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?? 0;
        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?? 0;
        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?? 0;
        $with = ['comments' => function ($q) use ($crmAdmin, $crmEditor, $crmAssistance, $crmRecruitment) {
            $q->orderBy('created_at', 'desc');
            if ((!$crmAdmin)) {
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
