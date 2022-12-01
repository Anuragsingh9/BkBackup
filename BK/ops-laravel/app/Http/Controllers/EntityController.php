<?php

namespace App\Http\Controllers;

use App\Entity;
use App\EntityType;
use App\EntityUser;
use App\Model\Contact;
use App\Model\EntityDependency;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Crm\Services\NotesService;
use Validator;
use Illuminate\Http\Request;

/**
 * Class EntityController
 * @package App\Http\Controllers
 */
class EntityController extends Controller {
    
    protected $ENTITIES = [
        'instance' => 1,
        'company'  => 2,
        'union'    => 3,
        'press'    => 4,
    ];
    protected $ENTITY_SUBTYPE = [
        'internal' => 1,
        'external' => 2,
    ];
    
    private $core, $notesService;
    
    
    public function __construct() {
        $this->notesService = NotesService::getInstance();
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }
    
    /**
     * @param Request $request
     * @param $val
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntity(Request $request, $val, $type, $belongId = 0) {
        if ((!empty($val) && !empty($type)) && strlen($val) >= 3) {
            try {
                if ($type == 'instance')
                    $type = 1;
                elseif ($type == 'company')
                    $type = 2;
                elseif ($type == 'union')
                    $type = 3;
                elseif ($type == 'press')
                    $type = 4;
                
                if ($belongId != 0) {
                    $entityUsers = EntityUser::where(function ($q) use ($belongId) {
                        $q->orWhere('user_id', $belongId)->orWhere('contact_id', $belongId);
                    })->get(['entity_id']);
                    $e = Entity::where(function ($q) use ($val) {
                        $q->orWhere('long_name', 'LIKE', '%' . $val . '%');
                        $q->orWhere('short_name', 'LIKE', '%' . $val . '%');
                        $q->orWhere(DB::raw("CONCAT(`long_name`, ' ', `short_name`)"), 'LIKE', '%' . $val . "%");
                        $q->orWhere('entity_description', 'LIKE', '%' . $val . '%');
                    })
                        ->where('entity_type_id', $type)
                        ->whereNotIn('id', $entityUsers->pluck('entity_id'))
                    ;
                    if ($type == 3 && $request->has('sub_type')) {
                        $entity = $e->where('entity_sub_type', $request->sub_type)
                            ->get(['id', 'long_name', 'short_name']);
                    } else {
                        $entity = $e->get(['id', 'long_name', 'short_name']);
                    }
                } else {
//                    dd('here');
                    $e = Entity::where(function ($q) use ($val) {
                        $q->orWhere('long_name', 'LIKE', '%' . $val . '%');
                        $q->orWhere('short_name', 'LIKE', '%' . $val . '%');
                        $q->orWhere(DB::raw("CONCAT(`long_name`, ' ', `short_name`)"), 'LIKE', '%' . $val . "%");
                        $q->orWhere('entity_description', 'LIKE', '%' . $val . '%');
                    })->where('entity_type_id', $type);
                    if ($type == 3 && $request->has('sub_type')) {
                        $entity = $e->where('entity_sub_type', $request->sub_type)
                            ->get(['id', 'long_name', 'short_name']);
                    } else {
                        $entity = $e->get(['id', 'long_name', 'short_name']);
                    }
                }
                
                return response()->json($entity);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
            }
        }
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPersonEntity(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'type'          => 'required',
                'entity_type'   => 'required',
                'person_id'     => 'required',
                'entity_id'     => 'required|exists:tenant.entities,id',
                'entity_old_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()
                    ->all())], 422); //validation false return errors
            }
            $type = 'user_id';
            
            if ($request->type == 'contact') {
                $type = 'contact_id';
            }
            
            if ($request->entity_old_id != 0) {
                $entityUserDel = EntityUser::where(['id' => $request->entity_old_id])->delete();
                if ($entityUserDel) {
                    $entityUserCount = EntityUser::where([$type => $request->person_id])->count();
                    if (($request->entity_type == 'union') || (($entityUserCount == 0) && ($request->entity_type == 'instance' || $request->entity_type == 'company'))) {
                        $entityUser = EntityUser::create([$type => $request->person_id, 'entity_label' => '', 'entity_id' => $request->entity_id]);
                        $entityUser['data_id'] = $request->entity_id;
                        return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
                    } else {
                        return response()->json(['status' => FALSE, 'msg' => ucfirst($request->type) . ' belongs to another ' . ucfirst($request->entity_type)], 422);
                    }
                    
                }
                return response()->json(['status' => FALSE, 'data' => ''], 500);
            } else {
                if ($request->entity_type != 'union') {
                    $entityUserCount = EntityUser::where([$type => $request->person_id, 'entity_id' => $request->entity_id])
                        ->count();
                    if ((($entityUserCount == 0) && ($request->entity_type == 'union' || $request->entity_type == 'company'))) {
                        $entityUser = EntityUser::create([$type => $request->person_id, 'entity_label' => '', 'entity_id' => $request->entity_id]);
                        return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
                    } else {
                        return response()->json(['status' => FALSE, 'msg' => ucfirst($request->type) . ' belongs to another ' . ucfirst($request->entity_type)], 422);
                    }
                } else {
                    $entityUser = EntityUser::create([$type => $request->person_id, 'entity_label' => '', 'entity_id' => $request->entity_id]);
                }
                
            }
            
            return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param $id
     * @param $entityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePersonEntity($id, $entityId) {
        try {
            if (!empty($id)) {
                $entityUser = EntityUser::whereId($id)->update(['entity_id' => $entityId]);
                return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
            }
            return response()->json(['status' => FALSE, 'data' => ''], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePersonEntity($id) {
        try {
            if (!empty($id)) {
                $entityUser = EntityUser::whereId($id)->delete();
                if ($entityUser)
                    return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
                else
                    return response()->json(['status' => FALSE, 'data' => ''], 500);
            }
            return response()->json(['status' => FALSE, 'data' => ''], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDependency(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'type'      => 'required',
                'parent_id' => 'required|exists:tenant.entities,id',
                'entity_id' => 'required|exists:tenant.entities,id',
            ]);
            $validator->after(function ($validator) use ($request) {
                $p = Entity::find($request->parent_id);
                $e = Entity::find($request->entity_id);
                if (($p && $e) && (($p->entity_type_id != $e->entity_type_id) || ($e->entity_type_id == 3 && ($e->entity_sub_type != $p->entity_sub_type)))) {
                    $validator->errors()
                        ->add('$request->parent_id', 'Parent Entity must be also ' . EntityType::find($e->entity_type_id)->name
                            . ($e->entity_type_id == 3 ? (($e->entity_sub_type == 1) ? ' Internal' : ' External') : '')
                        );
                }
            });
            
            //validation false return errors
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            if ($request->entity_id == $request->parent_id) {
                return response()->json(['status' => FALSE, 'msg' => "child must not same as parent"], 422);
            }
            $permissions = \Auth::user()->permissions;
            $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ? TRUE : 0;
            $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ? TRUE : 0;
            $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ? TRUE : 0;
            if (($crmAssistance || $crmRecruitment) && (!$crmAdmin)) {
                return response()->json(['status' => FALSE, 'msg' => __('message.com_dependency')], 422);
            }
            $entityDependency = EntityDependency::where('entity_id', $request->entity_id)->delete();
            $data = EntityDependency::create($request->all());
            return response()->json(['status' => TRUE, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeDependency($id) {
        try {
            if (!empty($id)) {
                $entityDependency = EntityDependency::where('entity_id', $id)->delete();
                //                $entityDependency = EntityDependency::whereId($id)->delete();
                return response()->json(['status' => TRUE, 'data' => $entityDependency], 200);
            }
            return response()->json(['status' => FALSE, 'data' => ''], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param $parent
     * @param $child
     * @return mixed
     */
    public function checkTypeCompany($parent, $child) {
        return Entity::whereIn('id', [$parent, $child])->where('entity_type_id', 2)->count();
    }
    
    public function getPersonsOfCompany($companyId) {
        $entity = Entity::find($companyId);
        if (!$entity) {
            return response()->json(['status' => FALSE, 'msg' => 'Invalid Entity Id'], 422);
        }
        $entity_name = $entity->entityType->name;
        $fields_to_get = ['id', 'entity_id', 'user_id', 'contact_id', 'entity_label', 'membership_type'];
        
        if ($entity_name == 'Unions') {
            $fields_to_get = ['id', 'entity_id', 'user_id', 'contact_id', 'entity_label', 'membership_type'];
        }
        $entityUser = EntityUser::with(['contact' => function ($a) {
            $a->orderBy('lname', 'asc');
        }, 'user'                                 => function ($b) {
            $b->orderBy('lname', 'asc');
        }])->where(['entity_id' => $companyId])->where(function ($query) {
            $query->whereHas('user')->orWhereHas('contact');
        })->get($fields_to_get);
        return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
    }
    
    public function getPersons($keyword, $belongId) {
        try {
            
            $result = [];
            $keyword = ltrim($keyword);
            $keyword = rtrim($keyword);
            if (!empty($keyword) && $belongId != 0) {
                $entityUsers = EntityUser::where(function ($q) use ($belongId) {
                    $q->where('entity_id', $belongId);
                })->get(['entity_id', 'user_id', 'contact_id']);
                
                //getting search results from Users table
                $users = User::where(function ($query) use ($keyword) {
                    $query->orWhere('fname', 'like', '%' . $keyword . '%')
                        ->orWhereRaw("CONCAT(fname,' ',lname) like '%$keyword%'")
                        ->orWhere('lname', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                })
                    ->whereNotIn('id', $entityUsers->pluck('user_id')->filter())
                    ->select(DB::raw("CONCAT(fname,' ',lname) AS name,email,id"))
                    ->get(['id', 'fname', 'lname', 'email']);
                //getting search results from Contact table
                $contacts = Contact::where(function ($query) use ($keyword) {
                    $query->orWhere('fname', 'like', '%' . $keyword . '%')
                        ->orWhereRaw("CONCAT(fname,' ',lname) like '%$keyword%'")
                        ->orWhere('lname', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                })
                    ->whereNotIn('id', $entityUsers->pluck('contact_id')->filter())
                    ->select(DB::raw("CONCAT(fname,' ',lname) AS name,email,id"))
                    ->get(['id', 'fname', 'lname', 'email']);
                $users->map(function ($user) {
                    $user['type'] = 'user';
                    return $user;
                });
                $contacts->map(function ($contact) {
                    $contact['type'] = 'contact';
                    return $contact;
                });
                $collection = collect($users);
                $merged = $collection->merge($contacts);
                $result = $merged->all();
            }
            return response()->json([
                'status' => TRUE,
                'data'   => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * To create the new entity
     *
     * 1. Validation
     * 2. Check if logo present then upload it to S3
     * 3. Create a entity data to database table [entities]
     * 4. Create a entity log to crm_notes table
     */
    public function createEntity(Request $request) {
        try {
            // 1. Validation
            $validator = Validator::make($request->all(), [
                'long_name'  => ['required', Rule::unique('tenant.entities')->where(function ($query) use ($request) {
                    return $query->where('entity_type_id', $this->ENTITIES[$request->type]);
                })],
                'short_name' => 'required',
                'type'       => 'required',
                'logo'       => 'nullable|image',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => FALSE,
                    'msg'    => implode(',', $validator->errors()->all()),
                ], 422); //validation false return errors
            }
            
            // 2. Upload the logo if present
            if ($request->hasFile('logo')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/' . $request->type;
                $filename = $this->core->fileUploadByS3($request->file('logo'), $folder, 'public');
                $request->merge(['entity_logo' => $filename]);
            }
            
            // 3. Create a entity model to database
            $entity = Entity::create([
                'long_name'          => $request->long_name,
                'short_name'         => $request->short_name,
                'entity_type_id'     => $this->ENTITIES[($request->type)],
                'email'              => $request->email,
                'entity_website'     => $request->website,
                'address1'           => isset($request->address) ? $request->address : $request->address1,
                'city'               => $request->city,
                'country'            => $request->country,
                'phone'              => isset($request->phone) ? $request->phone : $request->telephone,
                'industry_id'        => $request->industry_id,
                'entity_description' => $request->entity_description,
                'created_by'         => Auth::user()->id,
                'entity_logo'        => $request->entity_logo,
                'zip_code'           => $request->zip_code,
                'fax'                => $request->fax,
                'entity_sub_type'    => ($request->has('sub_type') ? $this->ENTITY_SUBTYPE[$request->sub_type] : NULL),
            ]);
            
            // 4. Generating Logs in crm_notes table
            if ($entity) {
                if (session()->get('lang') == 'EN') {
                    $note = $request->type . ' created on ' . getCreatedAtAttribute(Carbon::now()) . ' by ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                } else {
                    $note = $request->type . ' créée le ' . getCreatedAtAttribute(Carbon::now()) . ' par ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                }
                $this->notesService->addNote(['type' => 'entity', 'field_id' => $entity->id, 'notes' => $note]);
                return response()->json(['status' => TRUE, 'data' => $entity], 200);
            } else { // Entity not created so here
                return response()->json(['status' => FALSE, 'data' => []], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * 1. Validation
     * To Create
     *  1. Validate
     *  2. Check Duplicate
     *  3. Check Already have a company
     *  4. Create
     * To Update
     *  Check entity exists with same name
     * update
     */
    public function addPersonBelongsTo(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'type'          => 'required',
                'entity_type'   => 'required',
                'person_id'     => 'required',
                'entity_id'     => 'required|exists:tenant.entities,id',
                'entity_old_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()
                    ->all())], 422); //validation false return errors
            }
            $type = $request->type == 'contact' ? 'contact_id' : 'user_id';
            $create_columns = [
                $type          => $request->person_id,
                'entity_id'    => $request->entity_id,
                'entity_label' => $request->position,
            ];
            $update_columns = [
                'entity_id'    => $request->entity_id,
                'entity_label' => $request->position,
            ];
            
            $entity_type = Entity::find($request->entity_id)->entity_type_id;
            if (!($entity_type && $entity_type == $this->ENTITIES[$request->entity_type])) { // check if entity id provided is actually have the same type
                return response()->json(['status' => FALSE, 'msg' => 'Invalid Entity'], 422);
            }
            if ($request->entity_type == 'union') {
                $create_columns['membership_type'] = isset($request->member_type) ? $request->member_type : '';
                $update_columns['membership_type'] = isset($request->member_type) ? $request->member_type : '';
            }
            if ($request->entity_old_id == 0) { // To a new relation of user with entity
                // Check duplicate entry
                $entity_user_relation = EntityUser::where([
                    $type       => $request->person_id,
                    'entity_id' => $request->entity_id,
                ]);
                if ($entity_user_relation->count()) {
                    return response()->json([
                        'status' => TRUE,
                        'data'   => $entity_user_relation->first(),
                    ], 200);
                }
                // Done
                
                // Check already belongs to some company instance or press
                if ($request->entity_type != 'union') {
                    $entity_type = $request->entity_type;
                    $entity_user_count = EntityUser::whereHas('entity', function ($q) use ($entity_type) {
                        $q->where('entity_type_id', $this->ENTITIES[$entity_type]);
                    })->where($type, $request->person_id)->count();
                    if ($entity_user_count) {
                        return response()->json([
                            'status' => FALSE,
                            'msg'    => ucfirst($request->type) . ' already belongs to ' . ucfirst($request->entity_type),
                        ], 422);
                    }
                } // DONE
                $entityUser = EntityUser::create($create_columns);
                if ($entityUser) {
                    return response()->json(['status' => TRUE, 'data' => $entityUser], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'person not added to entity'], 500);
                }
            } else { // MODE = EDIT in person belongs to 1. Delete 2. Add new records
                //this is due to front end dev as they are sending some time entitUser id in entity_old and some time real entity id in entity_old_id
                $entity_user_update = EntityUser::where([
                    $type => $request->person_id,
                ])->where(function ($a) use ($request) {
                    $a->orWhere('id', $request->entity_old_id);
                    $a->orWhere('entity_id', $request->entity_old_id);
                });
                
                if (!$entity_user_update->count()) {
                    return response()->json(['status' => FALSE, 'msg' => 'No Previous Relation Found'], 500);
                }
                if (!$entity_user_update->update($update_columns)) {
                    return response()->json(['status' => FALSE, 'data' => ''], 500);
                }
                $entityUser = EntityUser::where([
                    $type       => $request->person_id,
                    'entity_id' => $request->entity_id,
                ])->first();
                
                return response()->json(['status' => TRUE, 'msg' => 'Added Person Belongs to', 'data' => $entityUser], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
        }
    }
}
