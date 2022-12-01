<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Newsletter\Entities\IcontactMeta;
use Validator;
use DB;
use Modules\Newsletter\Entities\Contact;
use Modules\Newsletter\Entities\Icontact_meta;
use Modules\Newsletter\Services\ContactServices;
use Modules\Newsletter\Services\IContactSingleton;
use Modules\Newsletter\Entities\Subscription;
use App\Model\ListModel;
use App\Services\ListServices;
class ContactController extends Controller
{
    private $instance,$IcontactInstance;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->Listinstance = ListServices::getInstance();
        $this->instance=ContactServices::getInstance();
        $this->IcontactInstance = IContactSingleton::getInstance();
        $this->middleware('IcontactCheck', ['only' => [
            'store','destroy','update','addContactInList' // Could add bunch of more methods too
        ]]);
    }
    /**
     * Display a listing of the Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contact = Contact::paginate(10);

        return response()->json(['status' => true, 'data' => $contact],200);
    }

    /**
     * Show the form for creating a new Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created Contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        try {
            $validator = Validator::make($request->all(), [
                'fname' => 'sometimes|required|regex:/^[A-ZÀÂÇÉÈÊËÎÏÔÛÙÜŸÑÆŒa-zàâçéèêëîïôûùüÿñæœ_.,() ]+$/|max:50|min:2',
                'lname' => 'sometimes|required|regex:/^[A-ZÀÂÇÉÈÊËÎÏÔÛÙÜŸÑÆŒa-zàâçéèêëîïôûùüÿñæœ_.,() ]+$/|max:50|min:2',
                //when release live please use tenant for database check in email unique funtion 
                'email' => 'required|email|regex:/^.+@.+$/i|max:100',
            ]);
            $subscription=[];  
                if($request->has('subscription_id')){
                   $subscription = Subscription::find($request->subscription_id);
                }
            $validator->after(function($validator) use($request)
            {
                if ($this->CheckUniqueEmail($request->email)>0)
                {
                    $validator->errors()->add('email', 'Email already exists!');
                }
            });
             $validator->after(function($validator) use($request,$subscription)
            {
                if(!$subscription){
                    $validator->errors()->add('subscription_id', 'Subscription form not found!');
                }
                
            });

            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400); 
            }
            DB::connection('tenant')->beginTransaction();
            //ADDING DATA TO DATABASE
            $contact = $this->instance->addContact(
                request('email'),
                request('fname')?request('fname'):'',
                request('lname')?request('lname'):'',
                request('company')?request('company'):'',
                request('instance')?request('instance'):'',
                request('union')?request('union'):''
                );
                
            // adding contact data in icontact
                if (!empty($contact)&& is_object($contact) && isset($contact->id)) {

                    $aContact=[[
                            'email'=>$contact->email,
                            'firstName'=>$contact->fname,
                            'lastName'=>$contact->lname,
                            'phone'=>'',

                        ]];
                    $iContact = $this->IcontactInstance->createContact($aContact);
                    // $data = (array)$iContact->getData();
                    if($request->has('subscription_id')){
                            $this->addContactInList($subscription,$iContact);
                             $attach=[$contact->id];
                            $list = ListModel::find($subscription->list_id);
                            $this->Listinstance->attachList($list, $attach);
                    }
                     $data = (array)$iContact;
                    // ADDING IDs INTO ICONTACT_METAS TABLE
                    $icontact_meta = IcontactMeta::create([
                        'type'=>1,
                        'column_id' => $contact-> id,
                        'icontact_id' => $data['contacts'][0]->contactId,
                        ]);
                DB::connection('tenant')->commit();
                if($request->has('type') && $request->type=='subscript' && $subscription){
                    if($contact){
                    return response()->json(['data'=>$contact,'status'=>true,'msg'=>$subscription->success_url ],200);
                    }
                    else{
                    return response()->json(['data'=>[],'status'=>false,'msg'=> $subscription->success_url],200);
                    }
                }
                else{
                   return response()->json(['data'=>$contact,'status'=>true,'msg'=> 'Contact Created Successfully!'],200); 
                }
                } 
            }  
        catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    public function addContactInList($subscription,$iContact){
        $data = (array)$iContact;
        $listId=$subscription->list_id;
        $icontactListId=IcontactMeta::where(['type'=>2,'column_id'=>$listId])->first() ;
        $this->IcontactInstance->addSubscriber([[
              "listId"=>$icontactListId->icontact_id,
              "contactId"=>$data['contacts'][0]->contactId,
              "status"=>"normal"
        ]]);
       
    }
    public function CheckUniqueEmail($email){
        return Contact::where('email',$email)->count();
    }

    /**
     * Display the specified Contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact,$id)
    {
        try{
            $contact = Contact::findOrfail($id);
            return response()->json(['data'=>$contact],200);
        }catch (\Exception $e){
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Show the form for editing the specified Contact.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified Contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fname' => 'required|regex:/^[A-ZÀÂÇÉÈÊËÎÏÔÛÙÜŸÑÆŒa-zàâçéèêëîïôûùüÿñæœ_.,() ]+$/|max:50|min:2',
                'lname' => 'required|regex:/^[A-ZÀÂÇÉÈÊËÎÏÔÛÙÜŸÑÆŒa-zàâçéèêëîïôûùüÿñæœ_.,() ]+$/|max:50|min:2',
                // 'status' => 'required|in:1,0',
                // 'email' => 'required|email|regex:/^.+@.+$/i|max:100',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400); 
            }

            return $this->instance->updateContact(
                $id,
                request('fname'),
                request('lname'),
                request('status')
            );
        }
        catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    /**
     * 
     * Remove the specified Contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::find($id);
            $contact->delete();
            return response()->json(['status'=>true,'msg'=> 'Contact Deleted Successfully'],200);
        } 
        catch (\Exception $e) {
            return response()->json(['status' => false,'data'=>$contact, 'msg' => $e->getMessage()], 500);
        }
    }
}