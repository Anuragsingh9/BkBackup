<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\Union;
use App\UnionContact;
use App\UnionAdmin;
use App\Entity;
use App\EntityUser;
use App\Model\Contact;
use Auth;
use Modules\Crm\Services\NotesService;
use Carbon\Carbon;
use Validator;

class UnionController extends Controller
{

    private $core, $notesService;

    public function __construct()
    {
        $this->notesService = NotesService::getInstance();
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    public function addUnion(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'union_name' => 'required',
            'logo' => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
        }
        if ($request->id > 0) {
            $res = $this->editUnion($request);
        } else {

            if ($request->hasFile('union_logo')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/union';

                $filename = $this->core->fileUploadByS3($request->file('union_logo'), $folder, 'public');
                $request->merge(['entity_logo' => $filename]);
            }
            if ($request->hasFile('logo')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/union';

                $filename = $this->core->fileUploadByS3($request->file('logo'), $folder, 'public');
                $request->merge(['entity_logo' => $filename]);
            }


            if (empty($request->address2)) {
                $request->address2 = null;
            }
            if (empty($request->industry_id)) {
                $request->industry_id = null;
            }
            if (empty($request->family_id)) {
                $request->family_id = null;
            }
            // Changes for CRM now union store in entitiy table By vijay 


            $insertData = [
                'address1' => $request->address1,
                'address2' => $request->address2,
                'city' => $request->city,
                'country' => $request->country,
                'email' => $request->email,
                'fax' => $request->fax,
                'industry_id' => $request->industry_id,
                'entity_logo' => $request->entity_logo,
               'zip_code' => $request->zip_code,
                'phone' => $request->telephone,
                'short_name' => $request->union_code,
                'entity_description' => $request->union_description,
                'long_name' => $request->union_name,
                'entity_ref_type' => $request->union_type,
                'entity_website' => $request->website,
                'entity_type_id' => 3
            ];
            $lastUnionId = Entity::insertGetId($insertData);
            if (session()->get('lang') == 'EN')
                $note = ucfirst('union') . ' created on ' . getCreatedAtAttribute(Carbon::now()) . ' by ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
            else
                $note = ucfirst('union') . ' créée le ' . getCreatedAtAttribute(Carbon::now()) . ' par ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
            $this->notesService->addNote(['type' => 'union', 'field_id' => $lastUnionId, 'notes' => $note]);
            // Comment as per new CRM working
            // if ($lastUnionId > 0) {
            //     $union_contacts = json_decode($request->union_contacts);
            //     if (!empty($union_contacts)) {
            //         $data = [];
            //         /* for crm now we insert contact in newsletter contact
            //          * add refrance entry to entity user table   
            //         */
            //         $entityUserData = [];
            //         foreach ($union_contacts as $k => $val) {
            //             // Check email already exists
            //             $contactExits = Contact::where('email', $val->email)->select('id')->first();
            //             if ($contactExits) {
            //                 $entityUserData[] = ['entity_id' => $lastUnionId, 'contact_id' => $contactExits->id, 'entity_label' => $val->position, 'created_by' => Auth::user()->id, 'created_at' => \Carbon\Carbon::now()];
            //             } else {
            //                 $data = ['fname' => empty($val->f_name) ? null : $val->f_name, 'lname' => empty($val->l_name) ? null : $val->l_name, 'email' => $val->email,  'status' => ($val->display == '') ? 1 : $val->display];
            //                 $contactId = Contact::insertGetId($data);
            //                 $entityUserData[] = ['entity_id' => $lastUnionId, 'contact_id' => $contactId, 'entity_label' => $val->position, 'created_by' => Auth::user()->id, 'created_at' => \Carbon\Carbon::now()];
            //             }
            //             EntityUser::insert($entityUserData);
            //         }
            //     }
            //     if (count(json_decode($request->admin_id)) > 0) {
            //         foreach (json_decode($request->admin_id) as $key => $value) {
            //             UnionAdmin::insert(['union_id' => $lastUnionId, 'admin_id' => $value->value]);
            //         }
            //     }
            // }
            return response()->json($lastUnionId);
        }
    }

    function editUnion($request)
    {
        $updateData = [
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'country' => $request->country,
            'email' => $request->email,
            'fax' => $request->fax,
            'industry_id' => $request->industry_id,

           'zip_code' => $request->zip_code,
            'phone' => $request->telephone,
            'short_name' => $request->union_code,
            'entity_description' => $request->union_description,
            'long_name' => $request->union_name,
            'entity_ref_type' => $request->union_type,
            'entity_website' => $request->website,

        ];
        if ($request->hasFile('union_logo')) {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $folder = $domain . '/uploads/union';
            $filename = $this->core->fileUploadByS3($request->file('union_logo'), $folder, 'public');
            $updateData['entity_logo'] = $filename;
        }
//        print_r($updateData);


        Entity::updateOrCreate(['id' => $request->id], $updateData);
        // comment as ler new CRM working
        // $union_contacts = json_decode($request->union_contacts);
        // foreach ($union_contacts as $k => $val) {
        //     $data = ['union_id' => $request->id, 'f_name' => $val->f_name, 'l_name' => $val->l_name, 'position' => $val->position, 'display' => ($val->display == '') ? 1 : $val->display];
        //     UnionContact::updateOrCreate(['id' => $val->id], $data);
        // }
        // if (count(json_decode($request->admin_id)) > 0) {
        //     UnionAdmin::where('union_id', $request->id)->delete();
        //     foreach (json_decode($request->admin_id) as $key => $value) {
        //         UnionAdmin::updateOrCreate(['union_id' => $request->id, 'admin_id' => $value->value]);
        //     }
        // }
        return 1;
    }

    public function getUnion()
    {
        $data = Entity::with('industry')->where('entity_type_id', 3)->get();

        return response()->json($data);
    }

    public function deleteUnion($id)
    {
        $res = 0;
        if (Union::where('id', $id)->delete())
            $res = 1;
        return response()->json($res);
    }

    public function getUnionById(Request $request)
    {
        $data = Entity::with('industry')->where('id', $request->id)->first();
        if ($data->logo != '')
            $data->logo = $this->core->getS3Parameter($data->logo, 2);

        return response()->json($data);
    }

    public function addUnionContacts(Request $request)
    {
    }
}
