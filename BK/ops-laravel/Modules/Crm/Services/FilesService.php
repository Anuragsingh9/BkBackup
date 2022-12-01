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
use Modules\Crm\Entities\CrmDocument;
use Modules\Crm\Entities\CrmNote;

class FilesService
{
    /**
     * SuperAdminSingleton constructor.
     */
    // private $contactServices;

   

    /**
     * Make instance of SuperAdmin singleton class
     * @return FilesService|null
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

    public function addFile($array)
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
        return $note = $field->documents()->create([
            'regular_document_id' => $array['regular_document_id'],
            'created_by' => Auth::user()->id,
        ]);
    }

    public function getFiles($id, $type)
    {
        $permissions = \Auth::user()->permissions;
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
        if(\Auth::user()->role=='M1' || \Auth::user()->role=='M0'){
            $crmAdmin=1;
        }
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?? 0;
        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?? 0;
        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?? 0;
        $with = ['documents' => function ($q) use ($crmAdmin, $crmEditor, $crmAssistance, $crmRecruitment) {
            $q->orderBy('created_at', 'desc');
//             if ((!$crmAdmin)) {
//                 if ((!$crmEditor) || $crmAssistance || $crmRecruitment) {
// //                    if ($crmAssistance || $crmRecruitment)
//                     $q->where('created_by', Auth::user()->id);
//                 }
//             }
        },'documents.createdBy'];

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
    
    public function removeFile($crmDocumentId) {
        $permissions = \Auth::user()->permissions;
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?1 :0;
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?1: 0;
//        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?1 :0;
//        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?1: 0;
    
        $crmDocumentBuilder= CrmDocument::with('regularDocument');
        if (!($crmAdmin || $crmEditor || in_array(Auth::user()->role, ['M0', 'M1'])) ) {
            $crmDocumentBuilder = $crmDocumentBuilder->where('created_by', Auth::user()->id);
        }
        $crmDocument = $crmDocumentBuilder->find($crmDocumentId);
        if($crmDocument) {
            $crmDocument->regularDocument()->delete();
            return (bool) $crmDocument->delete();
        }
        return false;
    }
}
