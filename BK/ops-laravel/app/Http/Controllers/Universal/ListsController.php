<?php

namespace App\Http\Controllers\Universal;

//use App\Model\Contact;
use App\Http\Controllers\CoreController;
use App\User;
use App\Workshop;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Newsletter\Entities\IcontactMeta;
use Modules\Newsletter\Services\IContactSingleton;
use Modules\Newsletter\Services\NewsletterSingleton;
use Modules\Newsletter\Services\ContactServices;
use Validator;
use Modules\Newsletter\Entities\Contact;
use Modules\Newsletter\Entities\NewsletterList;
use Modules\Newsletter\Entities\ScheduleTime;
use App\Model\ListModel;
use App\Services\ListServices;
use Carbon\Carbon;
use Modules\Newsletter\Entities\ContactStatus;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

class ListsController extends Controller
{
    private $instance, $iContact, $core;
    /**
     * Create a new controller instance.
     * @return void
     */

    //CREATING CONSTRUCTOR
    public function __construct(NewsletterSingleton $newsletter)
    {
		$this->newsletter = $newsletter;
        $this->instance = ListServices::getInstance();
        $this->iContact = IContactSingleton::getInstance();
        $this->contact = ContactServices::getInstance();
        $this->core = app(CoreController::class);
        $this->middleware('IcontactCheck', ['only' => [
            'store','destroy','update','getStats' // Could add bunch of more methods too
        ]]);
    }

    /**
     * Display a listing of the list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $newsletter_list = ListModel::withCount('newsletter_contacts', 'users')->with(['icontact_meta' => function ($q) {
                $q->where('type', 2);
            }])->where('type', '!=', 2)->orderBy('id', 'desc')->get(['id', 'name', 'description', 'type', 'typology_id']);
            return response()->json(['status' => true, 'data' => $newsletter_list], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created list in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50|min:2',
                // 'type' => 'in:1,0', //as dan asked to create two list
                'typology_id' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $listcheck = ListModel::where(['name' => 'EXT-' . $request->name, 'name' => 'INT-' . $request->name])->get(['id'])->toArray();
            if (count($listcheck) > 0) {
                return response()->json(['status' => false, 'msg' => 'THE NAME HAS ALREADY BEEN TAKEN.'], 400);
            }
            //start transaction for skip the wrong entry
            $newsletter_list = DB::transaction(function () {
                //ADDING DATA TO DATABASE
                $type = 'EXT-';
                $listType = 1;
                //looping for adding two list
                for ($i = 1; $i <= 2; $i++) {
                    $newsletter_list = $this->instance->addList(
                        $type . request('name'),
                        request('description'),
                        $listType,
                        request('typology_id'),
                        request('creation_type')
                    );
                    //adding list to icontact list
                    $iContactList = $this->iContact->addList([['name' => $type . request('name')]]);
//                    $iContactList = '';
                    $type = 'INT-';
                    $listType = 0;
                    if (isset($iContactList->lists[0]->listId)) {
                        //adding reference in meta table
                        IcontactMeta::create(['column_id' => $newsletter_list->id, 'icontact_id' => $iContactList->lists[0]->listId, 'type' => 2]);
                    } else {
                        DB::rollBack();
                    }
                }
                return $newsletter_list;
            });

            if (!empty($newsletter_list)) {

                //checking if have workshop parameters
                if ($request->has("workshops") && count(json_decode(request('workshops'))) > 0 && isset(json_decode(request('workshops'))[0])) {
                    return $this->listFromWorkshop(json_decode(request('workshops')), $newsletter_list);
                }
                //checking if have all internal
                if (isset($request->all_internal) && $request->all_internal == true) {
                    return $this->listFromWorkshop([], $newsletter_list, $request->all_internal);
                }
                $externalList = ListModel::where('name', 'EXT-' . request('name'))->first();
                return response()->json(['data' => $externalList, 'status' => true, 'msg' => 'List Created Successfully!'], 200);
            } else {
                return response()->json(['status' => false, 'msg' => 'Something Went Wrong'], 500);
            }

        } catch (\Exception $e) {
          //  dd($e->getTraceAsString());
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified list.
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $listModel = ListModel::find($id, ['id', 'name', 'description', 'type', 'typology_id']);
        try {
	 	if($listModel!=null){
            if ($listModel->type) {
                $subscriber = $listModel->load(['newsletter_contacts' => function ($q) {
                    $q->addselect('newsletter_contacts.id', 'newsletter_contacts.email', 'newsletter_contacts.fname', 'newsletter_contacts.lname', 'newsletter_contacts.created_at')->orderBy('lname', 'asc');
                }]);
            } else {
                $subscriber = $listModel->load(['users' => function ($q) {
                    $q->addselect('users.id', 'users.email', 'users.fname', 'users.lname', 'users.created_at')->orderBy('lname', 'asc');
                }]);
            }
            return response()->json(['status' => true, 'data' => $subscriber], 200);
            }
        else{
            return response()->json(['status' => false, 'data' => []], 200);
        }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }

    }


    /**
     * Show the form for editing the specified list.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $list = ListModel::find($id);
            if($list){
                $icontactId=IcontactMeta::where(['column_id' => $id, 'type' => 2])->first();
                $list->icontactId=($icontactId!=null)?$icontactId->icontact_id:'';
            }
            return response()->json(['status' => true, 'data' => $list], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified list in storage.
     * Update the specified list in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50|min:2',
                'type' => 'required|in:1,0',
                'typology_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $listcheck = ListModel::where(['name' => 'EXT-' . $request->name, 'name' => 'INT-' . $request->name])->where('id', '!=', $id)->get(['id'])->toArray();
            if (count($listcheck) > 0) {
                return response()->json(['status' => false, 'msg' => 'THE NAME HAS ALREADY BEEN TAKEN.'], 400);
            }
            //UPDATING DATA IN DATABASE
            $list = $this->instance->updateList(
                $id,
                request('name'),
                request('description'),
                request('type'),
                request('typology_id')
            );
            if ($list) {
                $icontact = IcontactMeta::where(['column_id' => $id, 'type' => 2])->first(['icontact_id']);
                $this->iContact->updateList($icontact->icontact_id, ['name' => request('name')]);
            }

            return response()->json(['status' => true, 'data' => $list, 'msg' => 'List Updated Successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified NewsletterList from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //DELETING DATA FROM DATA USING ID
        try {
            $this->deleteContactFromAllList($id);
            $list = ListModel::find($id)->delete();
            if ($list) {
                $icontact = IcontactMeta::where(['column_id' => $id, 'type' => 2])->first(['icontact_id']);
                $this->iContact->deleteList($icontact->icontact_id);
            }
            return response()->json(['status' => true, 'msg' => 'List Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $list, 'msg' => $e->getMessage()], 500);
        }
    }

    public function deleteContactFromAllList($id)
    {
        $list = ListModel::with('newsletter_contacts', 'users')->find($id);
        $attach = [];
        $data = [];
        if ($list->type == 1) {
            $listAll = ListModel::where('type', 4)->first();
            foreach ($list->newsletter_contacts as $item) {
                $attach[] = $item->id;
            }
            $data = DB::connection('tenant')->table('listablesls')->where('list_model_id', $listAll->id)->whereIn('listablesls_id', $attach)->orderBy('listablesls_id')->get();
        } else {
            $listAll = ListModel::where('type', 3)->first();
            foreach ($list->users as $item) {
                $attach[] = $item->id;
            }
            $data = DB::connection('tenant')->table('listablesls')->where('list_model_id', $listAll->id)->whereIn('listablesls_id', $attach)->orderBy('listablesls_id')->get();
        }
        $id = 0;
        //  var_dump($data);die;
        foreach ($data as $key => $value) {
            if ($value->listablesls_id != $id) {
                $delete = DB::connection('tenant')->table('listablesls')->where('id', $value->id)->delete();
                $id = $value->listablesls_id;

            }
        }
    }

    /**
     * This function is used to add users or contacts'
     * with list in listable  table.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addList(Request $request)
    {
        try {
            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'list_id' => 'required',
                'list_type' => 'required|in:0,1',
                'field_id' => 'required|numeric'
            ]);
            // $validator->after(function ($validator) {
            //     if ($this->somethingElseIsInvalid()) {
            //         $validator->errors()->add('field', 'Something is wrong with this field!');
            //     }
            // });
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $list = ListModel::find($request->list_id);
            // for add external contact in list
            if ($request->list_type == 1) {
                $listAll = ListModel::where('type', 4)->first();
                $icontactAll = IcontactMeta::where(['column_id' => $listAll->id, 'type' => 2])->first(['icontact_id']);
                $icontact = IcontactMeta::where(['column_id' => $request->list_id, 'type' => 2])->first(['icontact_id']);
                $icontactUser = IcontactMeta::where(['column_id' => $request->field_id, 'type' => 1])->first(['icontact_id']);
                // checking contact already icontact or not
                $subscribeAll = [];
                if ($icontactUser) {
                    // if contact already in icontact then add in list
                    $subscribe[] = [
                        "listId" => $icontact->icontact_id,
                        "contactId" => $icontactUser->icontact_id,
                        "status" => "normal"
                    ];
                    $subscribeAll[] = [
                        "listId" => $icontactAll->icontact_id,
                        "contactId" => $icontactUser->contactId,
                        "status" => "normal"
                    ];
                } else {
                    //if contact not in list then add in contact and add in list
                    $newsletter_list = Contact::where('id', $request->field_id)->first();
                    $iContacts = $this->iContact->createContact([[
                        'email' => $newsletter_list->email,
                        'firstName' => $newsletter_list->fname,
                        'lastName' => $newsletter_list->lname,
                        'test3' => $newsletter_list->id
                    ]]);
                    if (isset($iContacts) && (count($iContacts->contacts) > 0)) {
                        foreach ($iContacts->contacts as $iContact) {
                            $subscribe[] = [
                                "listId" => $icontact->icontact_id,
                                "contactId" => $iContact->contactId,
                                "status" => "normal"
                            ];
                            $subscribeAll[] = [
                                "listId" => $icontactAll->icontact_id,
                                "contactId" => $iContact->contactId,
                                "status" => "normal"
                            ];
                            $icontactMeta[] = [
                                'type' => 1,
                                'column_id' => $iContact->test3,
                                'icontact_id' => $iContact->contactId,
                                'created_at' => $iContact->createDate,
                            ];
                        }
                    }
                    IcontactMeta::insert($icontactMeta);
                }
                $data = $this->iContact->addSubscriber($subscribe);
                $dataAll = $this->iContact->addSubscriber($subscribeAll);
                $attach[] = $request->field_id;
                $this->instance->attachList($list, $attach);
                $this->instance->attachList($listAll, $attach);
                // Create relation between list and listablesls
                // save list of contact in database with attach method
                // $status=$list->newsletter_contacts()->attach([
                //     $request->field_id
                // ]);

            } else {
                // save list of user in database with attach method
                //here we are adding these users to Icontact as a Contact
                $listAll = ListModel::where('type', 3)->first();
                $icontactAll = IcontactMeta::where(['column_id' => $listAll->id, 'type' => 2])->first(['icontact_id']);
                $user = User::find($request->field_id);
                $icontactUser = IcontactMeta::where(['column_id' => $request->field_id, 'type' => 1])->first(['icontact_id']);
                $icontact = IcontactMeta::where(['column_id' => $request->list_id, 'type' => 2])->first(['icontact_id']);
                if ($icontactUser) {
                    $subscribe[] = [
                        "listId" => $icontact->icontact_id,
                        "contactId" => $icontactUser->icontact_id,
                        "status" => "normal"
                    ];
                    $subscribeAll[] = [
                        "listId" => $icontactAll->icontact_id,
                        "contactId" => $icontactUser->contactId,
                        "status" => "normal"
                    ];
                    $attach[] = $request->field_id;
                } else {

                    $iContacts = $this->iContact->createContact([[
                        'email' => $user->email,
                        'firstName' => $user->fname,
                        'lastName' => $user->lname,
                        'phone' => $user->phone,
                        'ops_id' => $user->id
                    ]]);
                    if (isset($iContacts) && (count($iContacts->contacts) > 0)) {
                        //we need to update this custom field id later
                        foreach ($iContacts->contacts as $iContact) {
                            $icontactMeta[] = [
                                'type' => 6,
                                'column_id' => $iContact->ops_id,
                                'icontact_id' => $iContact->contactId,
                                'created_at' => $iContact->createDate,
                            ];
                            $subscribe[] = [
                                "listId" => $icontact->icontact_id,
                                "contactId" => $iContact->contactId,
                                "status" => "normal"
                            ];
                            $subscribeAll[] = [
                                "listId" => $icontactAll->icontact_id,
                                "contactId" => $iContact->contactId,
                                "status" => "normal"
                            ];
                            $attach[] = $iContact->ops_id;
                        }
                        //Inserting data in Meta table
                        IcontactMeta::insert($icontactMeta);
                        //adding users to list in iContact
                    }


                }
                $this->iContact->addSubscriber($subscribe);
                $this->iContact->addSubscriber($subscribeAll);
                //adding the same relationship in db (list and contact)
                $this->instance->attachList($list, $attach);
                $this->instance->attachList($listAll, $attach);
                //    $list->users()->attach([
                //     $request->field_id
                // ]);
            }
            return response()->json(['status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }

    }


    /**
     * this function is extended code of add list
     * in this function we are adding workshops users
     * in list and after that create their relationship
     * in Db.
     * @param array $workshops
     * @param $list
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFromWorkshop(array $workshops, $list, $allInternal = false)
    {
        if ((count($workshops) > 0) || $allInternal) {

            $listId = IcontactMeta::where(['type' => 2, 'column_id' => $list->id])->first()->icontact_id;
            if ($allInternal) {
                $users = Workshop::with('meta:id,user_id,role,workshop_id')->get(['id', 'workshop_name']);
            } else {
                $users = Workshop::whereIn('id', $workshops)->with('meta:id,user_id,role,workshop_id')->get(['id', 'workshop_name']);
            }
            //this functions create an array of users to add in Icontact
            $contacts = $this->instance->addContactWorkshops($users);
            //here we are adding these users to Icontact as a Contact
            $iContacts = $this->iContact->createContact(unique_multidim_array($contacts, 'email'));

            $listAll = ListModel::where('type', 3)->first();
            $icontactAll = IcontactMeta::where(['column_id' => $listAll->id, 'type' => 2])->first(['icontact_id']);
            if (isset($iContacts) && (count($iContacts->contacts) > 0)) {
                //we need to update this custom field id later
                foreach ($iContacts->contacts as $iContact) {
                    $icontactMeta[] = [
                        'type' => 6,
                        'column_id' => $iContact->test3,
                        'icontact_id' => $iContact->contactId,
                        'created_at' => $iContact->createDate,
                    ];
                    $subscribe[] = [
                        "listId" => $listId,
                        "contactId" => $iContact->contactId,
                        "status" => "normal"
                    ];
                    $subscribeAll[] = [
                        "listId" => $icontactAll->icontact_id,
                        "contactId" => $iContact->contactId,
                        "status" => "normal"
                    ];
                    $attach[] = $iContact->test3;
                }
                //Inserting data in Meta table
                IcontactMeta::insert($icontactMeta);
                //adding users to list in iContact
                $this->iContact->addSubscriber($subscribe);
                $this->iContact->addSubscriber($subscribeAll);
                //adding the same relationship in db (list and contact)
                $this->instance->attachList($list, $attach);
                $this->instance->attachList($listAll, $attach);

            }
            return response()->json(['data' => $list, 'status' => true, 'msg' => 'List Created Successfully!'], 200);
        }
        return response()->json(['status' => false, 'msg' => 'Something Went Wrong'], 500);
    }

    public function personList(Request $request)
    {
        try {
            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'type' => 'required|in:1,0'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }

            if ($request->type == 0) {
                $lists = User::where('id', $request->id)->with(['lists' => function ($q) {
                    $q->where('type', '!=', 3);
                }])->first(['id']);
            } else {
                $lists = Contact::where('id', $request->id)->with(['lists' => function ($q) {
                    $q->where('type', '!=', 4);
                }])->first(['id']);
            }
            return response()->json(['status' => true, 'data' => $lists], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getTypology()
    {
        $typologies = DB::connection('tenant')->table('newsletter_typology')->get(['id', 'name']);
        return response()->json(['status' => true, 'data' => $typologies], 200);
    }

    /**
     * this function is search in list
     * in this function we are searching list with subscriber
     * @param string $searchKeyword
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchList($searchKeyword)
    {
        try {
            $newsletter_list = ListModel::
            withCount('newsletter_contacts', 'users')
                ->where('name', 'like', '%' . $searchKeyword . '%')
                ->where('type', '!=', 2)
                ->get('id');
            $newsletter_list->map(function($q) {
                if($q->users_count != 0 && $q->newsletter_contacts_count == 0) {
                    $q->newsletter_contacts_count = $q->users_count;
                }
                $q->subscriber_count = ($q->users_count == 0 ? $q->newsletter_contacts_count : $q->users_count);
                return $q;
            });
            return response()->json(['status' => true, 'data' => $newsletter_list], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function searchContactExternalList($searchKeyword)
    {
        try {
            // $newsletter_list = ListModel::withCount('newsletter_contacts', 'users')->where('name','like','%'.$searchKeyword.'%')->get('id');
            $newsletter_list = Contact::where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $searchKeyword . '%')->groupBy('email')->get();
            return response()->json(['status' => true, 'data' => $newsletter_list], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function addContactToExternalList(Request $request)
    {
        try {
            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'fname' => 'required',
                'lname' => 'required',
                'list_id' => 'required',
                'list_type' => 'required|in:1,0'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $list = ListModel::find($request->list_id);
            $contact = $this->contact->addContact($request->email, $request->fname, $request->lname);
            $icontact = IcontactMeta::where(['column_id' => $request->list_id, 'type' => 2])->first(['icontact_id']);
            $iContacts = $this->iContact->createContact([[
                'email' => $this->core->Unaccent($contact->email),
                'firstName' => $contact->fname,
                'lastName' => $contact->lname,
                'test3' => $contact->id
            ]]);
            $listAll = ListModel::where('type', 4)->first();
            $icontactAll = IcontactMeta::where(['column_id' => $listAll->id, 'type' => 2])->first(['icontact_id']);
            if (isset($iContacts) && (count($iContacts->contacts) > 0)) {
                //we need to update this custom field id later
                foreach ($iContacts->contacts as $iContact) {
                    $icontactMeta[] = [
                        'type' => 1,
                        'column_id' => $iContact->test3,
                        'icontact_id' => $iContact->contactId,
                        'created_at' => $iContact->createDate,
                    ];
                    $subscribe[] = [
                        "listId" => $icontact->icontact_id,
                        "contactId" => $iContact->contactId,
                        "status" => "normal"
                    ];
                    $subscribeAll[] = [
                        "listId" => $icontactAll->icontact_id,
                        "contactId" => $iContact->contactId,
                        "status" => "normal"
                    ];
                    $attach[] = $iContact->test3;
                }
                //Inserting data in Meta table
                IcontactMeta::insert($icontactMeta);
                //adding users to list in iContact
                $this->iContact->addSubscriber($subscribe);
                $this->iContact->addSubscriber($subscribeAll);
                //adding the same relationship in db (list and contact)
                $this->instance->attachList($list, $attach);
                $this->instance->attachList($listAll, $attach);
                return response()->json(['status' => true, 'data' => $request->list_id], 200);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function fetchListByUser($id)
    {
        try {
            $user = User::with('lists')->where('id', $id)->first(['id']);
            return response()->json(['status' => true, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }


    }

    public function createDefaultList()
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $checkDefault = ListModel::where('type', 2)->get()->pluck(['id']);
            if (count($checkDefault) > 0) {
                return IcontactMeta::whereIn('column_id', $checkDefault)->get()->toArray();
            } else {
                $list = ListModel::create(["name" => "default-list", 'type' => 2, 'description' => 'this is default list',
                    'type' => 2, 'typology_id' => 1]);
                $icontactList = $this->iContact->addList([["name" => "default-list"], ["name" => "default-list2"]]);
                $meta = [];

                foreach ($icontactList->lists as $k => $data) {
                    $meta[] = [
                        'column_id' => $list->id,
                        'icontact_id' => $data->listId,
                        'type' => 2
                    ];
                }

                $IcontactMetalist = IcontactMeta::insert($meta);
                $user = User::where('role', 'M0')->first();
                $icontactContact = $this->iContact->createContact([[
                    'email' => $user->email,
                    'firstName' => $user->fname,
                    'lastName' => $user->lname,
                    'ops_id' => $user->id
                ]]);
                // var_dump($icontactContact);die;
                foreach ($icontactContact->contacts as $k => $data) {
                    $metaC = [
                        'column_id' => $user->id,
                        'icontact_id' => $data->contactId,
                        'type' => 6
                    ];
                }
                $IcontactMeta = IcontactMeta::create($metaC);
                $this->iContact->addSubscriber([[
                    "listId" => $meta[0]['icontact_id'],
                    "contactId" => $IcontactMeta->icontact_id,
                    "status" => "normal"
                ]]);
                DB::connection('tenant')->commit();

                return $meta;
            }
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function searchAllcontact($searchkeyword)
    {
        try {
            $newsletter_list = Contact::where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $searchkeyword . '%')->groupBy('email')->get();
            $data = User::where('role', '!=', 'M3')->where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $searchkeyword . '%')->groupBy('email')->get();
            $totalRecord = $newsletter_list->merge($data);
            return response()->json(['status' => true, 'data' => $totalRecord], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }
    public function getBounceOrSubscribe(){
        return ContactStatus::all()->pluck('icontact_id')->toArray();
    }
    public function getStats($listId)
    {
        try {
            $list = ListModel::with('newsletter_contacts', 'users')->withCount('newsletter_contacts', 'users')->where('id', $listId)->first();

            $icontactContctIds=[];
            if($list->type==1){
            	$contactIds=$list->newsletter_contacts->pluck('id');
            	$icontactContctIds = IcontactMeta::where(['type' => 1])->whereIn('column_id', $contactIds)->get()->pluck('icontact_id')->toArray();
            }
            else{
            	$contactIds=$list->users->pluck('id');
            	$icontactContctIds = IcontactMeta::where(['type' => 6])->whereIn('column_id', $contactIds)->get()->pluck('icontact_id')->toArray();
            }
            $nslList = NewsletterList::where('list_id', $listId)->get()->pluck('newsletter_id');
            $Schedule = ScheduleTime::whereIn('newsletter_id', $nslList)->get();
            $ScheduleTime=[];
            foreach($Schedule as $k=>$item){
                $ScheduleTime[$item->newsletter_id]=$item->getOriginal()['schedule_time'];
            }

            $icontactMessageId = IcontactMeta::where(['type' => 3])->whereIn('column_id', $nslList)->get();

            // dd($list);
            $getStats = [];
            $getStats['months'] = [];

            foreach ($icontactMessageId as $value) {
                $time = isset($ScheduleTime[$value->column_id]) ? $ScheduleTime[$value->column_id] : $value->created_at;
                $month = Carbon::parse($time)->format('M');
                if(!in_array($month,$getStats['months'])){
                    $getStats['months'][] = $month;
                }

                $res = $this->iContact->getStatistics($value->icontact_id);
                // dd($res,$value);
                $getStats[$month]['bounces']=isset($getStats[$month]['bounces'])?$getStats[$month]['bounces']:0;
              	$getStats[$month]['unsubscribes']=isset($getStats[$month]['unsubscribes'])?$getStats[$month]['unsubscribes']:0;
				$Bouncesids=[];
				$Unsubscribesids=[];
                $getBounces=$this->iContact->getBounces($value->icontact_id);
                $getUnsubscribes=$this->iContact->getUnsubscribes($value->icontact_id);
              	$Bouncesids=array_merge($Bouncesids,array_column($getBounces->bounces, 'contactId'));
              	$Unsubscribesids=array_merge($Unsubscribesids,array_column($getUnsubscribes->unsubscribes, 'contactId'));



                array_filter($Bouncesids,function($el)  use (&$icontactContctIds,&$month,&$getStats){
                    if (in_array($el, $icontactContctIds)) {
                     if (isset($getStats[$month]['bounces'])) {
                    		$getStats[$month]['bounces'] = $getStats[$month]['bounces'] + 1;
		                } else {
		                    $getStats[$month]['bounces'] = 1;
		                }
                    }
                });
                array_filter($Unsubscribesids,function($el)  use (&$icontactContctIds,&$month,&$getStats){
                    if (in_array($el, $icontactContctIds)) {
                       if (isset($getStats[$month]['unsubscribes'])) {
                    		$getStats[$month]['unsubscribes'] = $getStats[$month]['unsubscribes'] + 1;
		                } else {
		                    $getStats[$month]['unsubscribes'] = 1;
		                }
                    }
                });

                if (isset($getStats[$month]['delivered'])) {
                    $getStats[$month]['delivered'] = $getStats[$month]['delivered'] + $res->statistics->delivered;
                } else {
                    $getStats[$month]['delivered'] = $res->statistics->delivered;
                }

                // if (isset($getStats[$month]['unsubscribes'])) {
                //     $getStats[$month]['unsubscribes'] = $getStats[$month]['unsubscribes'] + $res->statistics->unsubscribes;
                // } else {
                //     $getStats[$month]['unsubscribes'] = $res->statistics->unsubscribes;
                // }
                if (isset($getStats[$month]['complaints'])) {
                    $getStats[$month]['complaints'] = $getStats[$month]['complaints'] + $res->statistics->complaints;
                } else {
                    $getStats[$month]['complaints'] = $res->statistics->complaints;
                }

                $getStats[$month]['subscribe'] = ($list->type == 1) ? $list->newsletter_contacts_count : $list->users_count;

            }
            $getStats['months'] = array_unique($getStats['months']);
            return response()->json(['status' => true, 'data' => $getStats], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }


    }

    public function removeUserList(Request $request)
    {
        try {
            $list = ListModel::find($request->listId);
            $icontactListId = IcontactMeta::where(['column_id' => $request->listId, 'type' => 2])->first(['icontact_id']);
            if ($request->list_type == 1) {
                $listAll = ListModel::where('type', 4)->first();
                $icontactUser = IcontactMeta::where(['column_id' => $request->userID, 'type' => 1])->first(['icontact_id']);
            } else {
                $listAll = ListModel::where('type', 3)->first();
                $icontactUser = IcontactMeta::where(['column_id' => $request->userID, 'type' => 6])->first(['icontact_id']);
            }

            // $res=$this->iContact->deleteContact($icontactUser->icontact_id);
            $postData = [
                "status" => "unsubscribed"
            ];
            $subScriptionId = $icontactListId->icontact_id . '_' . $icontactUser->icontact_id;
            $res = $this->iContact->updateSubscriber($subScriptionId, $postData);
            if ($res) {
                $attach[] = $request->userID;
                // IcontactMeta::where('id',$icontactUser->id)->delete();
                $this->instance->detachList($list, $attach);
                $this->instance->detachList($listAll, $attach);
                return response()->json(['status' => true, 'data' => "Record Deleted"], 200);
            } else {
                return response()->json(['status' => fasle, 'data' => "Record Not Deleted"], 200);
            }
        } catch (\exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function getContact($id)
    {
        try {
            $contact = Contact::find($id, ['fname', 'lname', 'email', 'id']);
            if ($contact) {
                return response()->json(['status' => true, 'data' => $contact], 200);
            } else {
                return response()->json(['status' => false, 'msg' => 'No record Found!'], 200);
            }
        } catch (\exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    //Crm list
    public function crmFetchList()
    {
        try {
            //code...
            $data = ListModel::whereIn('type', [0, 1])->get(['id', 'name', 'type']);
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    //Add result In List
    public function crmAddResultExistingList(Request $request)
    {
        try {
            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'list_id' => 'required|exists:tenant.lists,id',
                'persons' => 'required',
                'type' => 'required',
            ]);

            if ($request->type == 'user') {
                $type = 6;
            } else {
                $type = 1;
            }
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            if ($type == 6) {
                $users = User::whereIn('id', array_column(json_decode($request->persons), 'user_id'))->get(['id', 'fname', 'lname', 'email']);
            } else {
                $users = Contact::whereIn('id', array_column(json_decode($request->persons), 'user_id'))->get(['id', 'fname', 'lname', 'email']);
            }

            $list = ListModel::find($request->list_id);

            $checkAlready = IcontactMeta::whereIn('column_id', $users->pluck('id'))->where('type', $type)->get(['column_id', 'icontact_id', 'created_at']);
            $already = [];
            $ids = [];
            foreach ($users as $k => $item) {
                if (!in_array($item->id, $checkAlready->pluck('column_id')->toArray())) {
                    $ids[($k)] = ['email' => $this->core->Unaccent($item->email), 'firstName' => $item->fname, 'lastName' => $item->lname, 'ops_id' => $item->id];
                } else {
                    $already[] = $item->id;
                }
            }
            //this is because list id is internal
            $iContact = IContactSingleton::getInstance();
            $icontactListId = IcontactMeta::where('column_id', $list->id)->where('type', 2)->first(['icontact_id']);

            if (count($ids) > 0) {
                $iContacts = $iContact->createContact($ids);
                if (isset($iContacts) && (count($iContacts->contacts) > 0)) {

                    //we need to update this custom field id later
                    foreach ($iContacts->contacts as $iContact) {
                        $icontactMeta[] = [
                            'type' => $type,
                            'column_id' => $iContact->ops_id,
                            'icontact_id' => $iContact->contactId,
                            'created_at' => $iContact->createDate,
                        ];
                        $subscribe[] = [
                            "listId" => $icontactListId->icontact_id,
                            "contactId" => $iContact->contactId,
                            "status" => "normal"
                        ];
                        $attach[] = $iContact->ops_id;
                    }

                    //Inserting data in Meta table
                    IcontactMeta::insert($icontactMeta);
                    //adding users to list in iContact
                    $addSub = new IContactSingleton;
                    $addSub->addSubscriber($subscribe);

                    //adding the same relationship in db (list and contact)
                    $this->attachList($list, $attach);

                }
            }
            if (count($already) > 0) {
                $checkInListAlready = ($type == 6) ? $list->users : $list->newsletter_contacts;
                //we need to update this custom field id later

                foreach ($already as $iContact) {
                    $key = array_search($iContact, $checkAlready->pluck('column_id')->toArray());
                    if ($key !== false && (!in_array($iContact, $checkInListAlready->pluck('id')->toArray()))) {
                        $subscribe[] = [
                            "listId" => $icontactListId->icontact_id,
                            "contactId" => $checkAlready[$key]->icontact_id,
                            "status" => "normal"
                        ];
                        $attach[] = $iContact;
                    }
                }

                //adding users to list in iContact
                $addSub = new IContactSingleton;
                if (isset($subscribe))
                    $addSub->addSubscriber($subscribe);

                //adding the same relationship in db (list and contact)
                if (isset($attach))
                    $this->attachList($list, $attach);
            }
            if (isset($attach)) {
                return response()->json(['status' => TRUE, 'data' => count($attach)], 200);
            } else {
                return response()->json(['status' => FALSE, 'data' => 0], 200);
            }


        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }


    public function crmAddResultNewList(Request $request)
    {
        try {
            //VALIDATION GOES HERE
            $validator = Validator::make($request->all(), [
                'users' => 'required',
                'contacts' => 'required',
                'list_name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $newsletter_list = [];
            $listcheck = ListModel::where(function ($q) use ($request) {
                $q->orWhere('name', 'EXT-' . $request->list_name)->orWhere('name', 'INT-' . $request->list_name);
            })->get(['id'])->toArray();
            if (count($listcheck) > 0) {
                return response()->json(['status' => false, 'msg' => 'THE LIST NAME HAS ALREADY BEEN TAKEN.'], 400);
            }
            //start transaction for skip the wrong entry
            $newsletter_list = DB::transaction(function () {
                //ADDING DATA TO DATABASE
                $type = 'EXT-';
                $listType = 1;
                //looping for adding two list
                for ($i = 1; $i <= 2; $i++) {
                    $newsletter_list = $this->instance->addList(
                        $type . request('list_name'),
                        request('description'),
                        $listType,
                        1,
                        request('creation_type')
                    );
                    //adding list to icontact list
                    $iContactList = $this->iContact->addList([['name' => $type . request('list_name')]]);
                    //                    $iContactList = '';
                    $type = 'INT-';
                    $listType = 0;
                    if (isset($iContactList->lists[0]->listId)) {
                        //adding reference in meta table
                        IcontactMeta::create(['column_id' => $newsletter_list->id, 'icontact_id' => $iContactList->lists[0]->listId, 'type' => 2]);
                    } else {
                        DB::rollBack();
                    }
                }
                return $newsletter_list;
            });

            if (!empty($newsletter_list)) {
                $externalList = ListModel::whereIn('id', [$newsletter_list->id, ($newsletter_list->id - 1)])->get();

                $users = User::whereIn('id', collect(json_decode($request->users))->where('type', 'user')->pluck('user_id'))->get(['id', 'fname', 'lname', 'email']);

                $contacts = Contact::whereIn('id', collect(json_decode($request->contacts))->where('type', 'contact')->pluck('user_id'))->get(['id', 'fname', 'lname', 'email']);
                $totalCount = 0;
                foreach ($externalList as $item1) {
                    if ($item1->type == 0) {
                        $type = 6;
                        $data = $users;
                    } else {
                        $type = 1;
                        $data = $contacts;
                    }

                    if ($data->count() > 0) {
                        $checkAlready = IcontactMeta::whereIn('column_id', $data->pluck('id'))->where('type', $type)->get(['column_id', 'icontact_id', 'created_at']);
                        $already = [];
                        $ids = [];
                        foreach ($data as $k => $item) {
                            if (!in_array($item->id, $checkAlready->pluck('column_id')->toArray())) {
                                $ids[($k)] = ['email' => $this->core->Unaccent($item->email), 'firstName' => $item->fname, 'lastName' => $item->lname, 'ops_id' => $item->id];
                            } else {
                                $already[] = $item->id;
                            }
                        }
                        //this is because list id is internal
                        $iContact = IContactSingleton::getInstance();
                        $icontactListId = IcontactMeta::where('column_id', $item1->id)->where('type', 2)->first(['icontact_id']);

                        if (count($ids) > 0) {
                            $iContacts = $iContact->createContact($ids);
                            if (isset($iContacts) && (!empty($iContacts->contacts)) && (count($iContacts->contacts) > 0)) {

                                //we need to update this custom field id later
                                foreach ($iContacts->contacts as $iContact) {
                                    $icontactMeta[] = [
                                        'type' => $type,
                                        'column_id' => $iContact->ops_id,
                                        'icontact_id' => $iContact->contactId,
                                        'created_at' => $iContact->createDate,
                                    ];
                                    $subscribe[] = [
                                        "listId" => $icontactListId->icontact_id,
                                        "contactId" => $iContact->contactId,
                                        "status" => "normal"
                                    ];
                                    $attach[] = $iContact->ops_id;
                                }

                                //Inserting data in Meta table
                                IcontactMeta::insert($icontactMeta);
                                //adding users to list in iContact
                                $addSub = new IContactSingleton;
                                $addSub->addSubscriber($subscribe);

                                //adding the same relationship in db (list and contact)
                                $this->attachList($item1, $attach);
                                $totalCount = $totalCount + count($attach);
                            } else {
                                if (isset($iContacts) && (count($iContacts->warnings) > 0)) {
                                    return response()->json(['status' => false, 'msg' => implode(',', $iContacts->warnings)], 400);
                                }
                            }
                        }
                        if (count($already) > 0) {
                            $checkInListAlready = ($type == 6) ? $item1->users : $item1->newsletter_contacts;
                            //we need to update this custom field id later

                            foreach ($already as $iContact) {
                                $key = array_search($iContact, $checkAlready->pluck('column_id')->toArray());
                                if ($key !== false && (!in_array($iContact, $checkInListAlready->pluck('id')->toArray()))) {
                                    $subscribe[] = [
                                        "listId" => $icontactListId->icontact_id,
                                        "contactId" => $checkAlready[$key]->icontact_id,
                                        "status" => "normal"
                                    ];
                                    $attach[] = $iContact;
                                }
                            }

                            //adding users to list in iContact
                            $addSub = new IContactSingleton;
                            if (isset($subscribe))
                                $addSub->addSubscriber($subscribe);

                            //adding the same relationship in db (list and contact)
                            if (isset($attach)) {
                                $this->attachList($item1, $attach);
                                $totalCount = $totalCount + count($attach);
                            }
                        }
                    }
                    $data = [];
                    $attach = [];
                }

                if (isset($attach)) {
                    return response()->json(['status' => TRUE, 'data' => ($totalCount)], 200);
                } else {
                    return response()->json(['status' => FALSE, 'data' => 0], 200);
                }
            } else {
                return response()->json(['status' => false, 'msg' => 'Something Went Wrong'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => '', 'msg' => $e->getMessage()], 500);
        }
    }

    public function attachList($list, $attach)
    {
        if ($list->type) {
            return $list->newsletter_contacts()->attach(array_unique($attach));
        }
        return $list->users()->attach(array_unique($attach));
    }
}
