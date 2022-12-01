<?php

namespace Modules\Crm\Http\Controllers;

use App\Model\SkillTabs;
use App\Entity;
use App\EntityUser;
use App\Model\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Services\CategoryService;
use App\User;
use App;
use Modules\Crm\Services\NotesService;
use Validator;
use DB;
use Auth;

/**
 * Class CategoryController
 * @package Modules\Crm\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var \Modules\Crm\Services\SuperAdmin|null
     */
    private $categoryService, $notesService;
    /**
     * @var App
     */
    private $UserController, $core;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->categoryService = CategoryService::getInstance();
        $this->notesService = NotesService::getInstance();
        $this->UserController = app(\App\Http\Controllers\UserController::class);
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }


    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            //start transaction for skip the wrong entry
            DB::connection('tenant')->beginTransaction();
            $contact = $this->categoryService->addContact($request->all());
            if (isset($contact->id)) {
                if (session()->get('lang') == 'EN')
                    $note = 'Person created on ' . getCreatedAtAttribute(Carbon::now()) . ' by' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                else
                    $note = 'Personne créée le ' . getCreatedAtAttribute(Carbon::now()) . ' par' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                $this->notesService->addNote(['type' => 'contact', 'field_id' => $contact->id, 'notes' => $note]);
                DB::connection('tenant')->commit();
                return response()->json([
                    'status' => TRUE,
                    'data' => $contact
                ], 200);
            } else {
                DB::connection('tenant')->rollBack();
                return $contact;
            }
            if ($request->type == 1) {

            } elseif ($request->type == 2) {
                //passing array of required fields

            } elseif ($request->type == 3) {
                //passing array of required fields
                $this->categoryService->addEntity($request->all(), 1);
            } elseif ($request->type == 4) {
                //passing array of required fields
                $this->categoryService->addEntity($request->all(), 2);
            } elseif ($request->type == 5) {
                //passing array of required fields
                $this->categoryService->addEntity($request->all(), 3);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()]);
        }
    }

    /**
     * fetch search entity detail.
     * @param id (string) =>resource id
     * @param type (string) =>type of data like user contact and entity
     * @return Response
     */
    public function entityDetailFetch($id, $type)
    {
        try {
            $validator = Validator::make(['id' => $id, 'type' => $type], [
                'id' => 'required',
                'type' => 'required',

            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            // set locale for localization
            App::setLocale(strtolower(session()->get('lang')));
            // check type
            $data = $this->categoryService->getResponse($type, $id);
            if ($data) {
                return response()->json(['status' => true, 'data' => $data], 200);
            } else {
                return response()->json(['status' => false, 'msg' => 'data not found'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * generate user response.
     * @return Response
     */
    public function generateUserResponse($user)
    {

        $response = [
            'id' => $user->id,
            'type' => 'user',
            'entityHeaderData' => ['name' => $user->fname . ' ' . $user->lname],
            'entityTabList' => [
                [
                    'type' => __('message.professional_tab'),
                    'data' => [
                        ['First Name' => ['field' => 'fname', 'value' => $user->fname]], ['Last Name' => ['field' => 'lname', 'value' => $user->lname]],
                        ['Email' => ['field' => 'email', 'value' => $user->email]]
                    ],
                    'custom' => false
                ],
                [
                    'type' => __('message.roles'),
                    'data' => [],
                    'custom' => false
                ],
            ],

        ];
        return $response;
    }

    /**
     * @description add User or conatct to entity user table
     * @parameter enitity_id
     * @parameter type
     * @parameter user_id
     * @parameter contact_id
     */
    public function AddToEntityUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'person_id' => 'required',
                'type' => 'required',
                'entity_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $data = ['entity_id' => $request->entity_id, 'user_id' => $request->user_id];
            if ($request->type == 'contact') {
                $data = ['entity_id' => $request->entity_id, 'contact_id' => $request->user_id];
            }
            $data = EntityUser::create($data);
            if ($data) {
                return response()->json(['status' => true, 'data' => $data], 200);
            }
            return response()->json(['status' => false, 'msg' => "data not insert"], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * @description add User or conatct to entity user table
     * @parameter enitity_id
     * @parameter type
     * @parameter user_id
     * @parameter contact_id
     */
    public function updateToEntityUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'person_id' => 'required',
                'type' => 'required',
                'entity_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $data = ['entity_id' => $request->entity_id, 'user_id' => $request->user_id];
            if ($request->type == 'contact') {
                $data = ['entity_id' => $request->entity_id, 'contact_id' => $request->user_id];
            }
            $data = EntityUser::create($data);
            if ($data) {
                return response()->json(['status' => true, 'data' => $data], 200);
            }
            return response()->json(['status' => false, 'msg' => "data not insert"], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateStaticData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'master_type' => 'required',
                'id' => 'required',
                'field' => 'required',
                'value' => 'required_unless:field,entity_logo',
            ]);
            //validation false return errors

            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            if (($request->value=='null' || $request->value==null) && ($request->field != 'entity_logo')) {
                return response()->json(['status' => false, 'msg' => 'The value field is required.'], 422);
            }
            if ($request->master_type == 'user') {
                if ($request->field == 'address' || $request->field == 'address1') {
                    $json_array = json_decode($request->value, true);
                    if ($json_array !== NULL) {
                        if (isset($json_array['zip_code'])) {
                            $json_array['postal'] = $json_array['zip_code'];
                            unset($json_array['zip_code']);
                        }

                        if (isset($json_array['address1'])) {
                            $json_array['address'] = $json_array['address1'];
                            unset($json_array['address1']);
                        }
                        $data = User::where('id', $request->id)->update($json_array);
                    } else
                        $data = User::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                } else{
                    $data = User::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                    $data = [trim($request->field)=>trim(($request->value!=null)?$request->value:'')];
                }
            } elseif ($request->master_type == 'contact') {
                if ($request->field == 'address' || $request->field == 'address1') {
                    $json_array = json_decode($request->value, true);
                    if ($json_array !== NULL) {
                        if (isset($json_array['zip_code'])) {
                            $json_array['postal'] = $json_array['zip_code'];
                            unset($json_array['zip_code']);
                        }

                        if (isset($json_array['address1'])) {
                            $json_array['address'] = $json_array['address1'];
                            unset($json_array['address1']);
                        }
                        $data = Contact::where('id', $request->id)->update($json_array);
                    } else
                        $data = Contact::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                } else{
                    $data = Contact::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                     $data = [trim($request->field)=>trim(($request->value!=null)?$request->value:'')];
                    }
            } else {
                $value = $request->value;
                if ($request->field == 'entity_logo' && $request->hasFile('value')) {

                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    $folder = $domain . '/uploads/' . strtolower($request->master_type);
                    $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                    $value = $filename;
                }
                if ($request->field == 'address' || $request->field == 'address1') {
                    $json_array = json_decode($request->value, true);
                    if ($json_array !== NULL) {
                        if (isset($json_array['address'])) {
                            $json_array['address1'] = $json_array['address'];
                            unset($json_array['address']);
                        }
                        $data = Entity::where('id', $request->id)->update($json_array);
                    } else
                        $data = Entity::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                } else
                    $data = Entity::where('id', $request->id)->update([trim($request->field) => $value]);
                if ($data) {
                    $data = Entity::with('industry')->find($request->id);
                }
            }
            if ($data)
                return response()->json(['status' => true, 'data' => $data], 200);
            else
                return response()->json(['status' => false, 'data' => 'Something Went Wrong.'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }

    public function getCategorySkills($id, $userId, $type)
    {
        try {
            $res = SkillTabs::where('id', $id)->where('is_valid', 1)->with(['skills' => function ($query) {
                $query->select('id', 'name', 'short_name', 'skill_format_id', 'skill_tab_id');
            }])->with(['skills.skillFormat' => function ($q) {
                $q->select('id', 'name_en', 'name_fr');
            }, 'skills.skillImages', 'skills.skillSelect', 'skills.skillCheckBox', 'skills.skillMeta', 'skills.skillCheckBoxAcceptance'])->with(['skills.userSkill' => function ($e) use ($type, $userId) {
                if ($type == 'user') {
                    $e->where('user_id', $userId);
                } else {
                    $e->where('field_id', $userId)->where('type', $type);
                }

            }])->first(['id', 'name']);

            return response()->json(['status' => true, 'data' => $res], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }

    }

    public function getPersonBelongsTo($personType, $personId) {
        $type = ($personType=='user'?'user_id' : ($personType=='contact' ? 'contact_id' : null));
        $validator = Validator::make(['id' => $personId, 'type' => $personType], [
            'id' => 'required|exists:tenant.' . ($personType=='user'?'users,id':'newsletter_contacts,id'),
            'type' => 'required|in:user,contact',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
        }
        $data = [];
        $where = [$type => $personId];
        $this->categoryService->getPersonBelongsResponse($data, $where);
        if($data != [])
            return response()->json(['status' => true, 'data' => $data], 200);
    }
}
