<?php

namespace Modules\Newsletter\Http\Controllers;

use App\Rules\Alphanumeric;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Newsletter\Entities\IContact;
use Modules\Newsletter\Entities\Sender;
use Modules\Newsletter\Entities\Newsletter;
use Modules\Newsletter\Services\IContactSingleton;
use Modules\Newsletter\Services\SenderSingleton;
use Validator;


/**
 * Class SenderController
 * @package Modules\Newsletter\Http\Controllers
 */
class SenderController extends Controller
{
    /**
     * SenderController constructor.
     * @param SenderSingleton $sender
     */
    public function __construct(SenderSingleton $sender)
    {
        $this->sender = $sender;
        $this->middleware('IcontactCheck', ['only' => [
            'store','destroy','update' // Could add bunch of more methods too
        ]]);
    }

    /**
     * Display a listing of the sender
     * @return Response
     */
    public function index()
    {
        try {
            // // Get sender list
            // $sender = $this->sender->getSenderList();
            // if ($sender['status'] == 201) {
            //     return response()->json(['status' => false, 'msg' => $sender['msg']], 201);
            // }
            // return response()->json(['status' => true, 'sender' => $sender], 200);

            $sender = Sender::with('user:id,fname,email,lname')->withCount('newsletters')->orderBy('id','desc')->get(['id', 'from_name', 'email', 'short_name', 'user_id']);

            return response()->json(['status' => true, 'sender' => $sender], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the from for creating a new sender.
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            // $name = $_GET['sender_name'];
            // // Search sender starting with 3 characters for search
            // $sender = $this->sender->getSearchDataForSender($name);
            // if (isset($sender['status']) && $sender['status'] == 201) {
            //     return response()->json(['status' => false, 'msg' => $sender['msg']], 201);
            // }
            // return response()->json(['status' => true, 'sender' => $sender], 200);

            $name = $_GET['sender_name'];

            // Search sender starting with 3 characters for search
            $sender = User::with('user:id')->where('fname', 'LIKE', "%" . $name . "%")->get(['id', 'fname', 'lname', 'email', 'role']);

            return response()->json(['status' => true, 'sender' => $sender], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $validator = validator::make(
                $request->all(),
                [
                    'user_id' => ['required'],
                    'short_name' => ['required', 'min:2', 'max:10', new Alphanumeric],
                    'from_name' => ['required', 'min:2', 'max:80'],
                    'email' => ['required', 'email', 'min:2', 'max:99'],
                ]
            );


            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }

            $userId = $request->user_id;
            $shortName = $request->short_name;
            $description = $request->description;
            $fromName = $request->from_name;
            $email = $request->email;
            $address = $request->address;
            $city = $request->city;
            $state = $request->state;
            $postal = $request->postal;
            $country = $request->country;

            // Add sender to database
            $sender = $this->sender->addSender($userId, $shortName, $fromName, $email, $description,$address,$city,$state,$postal,$country);

            //Added sender data in iContact parallelly
            $icontactSenderId = '';
            $instance = IContactSingleton::getInstance();
            $iSender = $instance->addSenderToIContact($sender);
            $iSender = json_decode($iSender);
            if (isset($iSender->campaigns[0]->campaignId)) {
                $icontactSenderId = $iSender->campaigns[0]->campaignId;
                $data = ['column_id' => $sender->id, 'icontact_id' => $icontactSenderId, 'type' => 0];
                IContact::create($data);
            }
            else if(isset($iSender->warnings) && count($iSender->warnings)>0){
                // $sender
                Sender::where('id',$sender->id)->delete();
                return response()->json(['status' => false, 'msg' => $iSender->warnings[0]], 500);
            }
            if ($sender['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $sender['msg']], 500);
            }
            return response()->json(['status' => true, 'sender' => $sender], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    { }

    /**
     * Show the from for editing the sender.
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            // // Fetch data of sender when showing edit screen
            // $sender = $this->sender->modifySender($id);
            // if (isset($sender['status']) && $sender['status'] == 201) {
            //     return response()->json(['status' => false, 'msg' => $sender['msg']], 201);
            // }
            // return response()->json(['status' => true, 'sender' => $sender], 200);


            // Fetch data of sender when showing edit screen

            $sender = Sender::with('user:id,fname,lname,email')->find($id);

            return response()->json(['status' => true, 'sender' => $sender], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified sender in storage.
     * @param  Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

        // Update sender data in iContact (We must need to use single instance for a controller so please create it in constructor instead of creating many time)
        try {
            $validator = validator::make(
                $request->all(),
                [
                    'user_id' => ['required'],
                    'short_name' => ['required', 'min:2', 'max:10', new Alphanumeric],
                    'from_name' => ['required', 'min:2', 'max:80'],
                    'email' => ['required', 'email', 'min:2', 'max:99'],
                ]
            );


            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $userId = $request->user_id;
            $shortName = $request->short_name;
            $description = $request->description;
            $fromName = $request->from_name;
            $email = $request->email;
            $address = $request->address;
            $city = $request->city;
            $state = $request->state;
            $postal = $request->postal;
            $country = $request->country;
            // Update sender in database
            $sender = $this->sender->updateSender($id, $userId, $shortName, $fromName, $email, $description,$address,$city,$state,$postal,$country);

            // Update sender data in iContact
            $instance = IContactSingleton::getInstance();
            $iSender = $instance->addSenderToIContact($sender, $id);
            $iSender = json_decode($iSender);
            if(isset($iSender->errors) && (count($iSender->errors)>0 || count($iSender->warnings)>0)){
                return response()->json(['status' => false, 'msg' => $iSender->errors[0]], 500);
            }
            if ($sender['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $sender['msg']], 500);
            }
            return response()->json(['status' => true, 'msg' => 'Data Updated Successfully',  'sender' => $sender], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified sender from storage.
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        try {

            // // Delete sender from database
            // $sender = $this->sender->deleteSender($id);
            // if ($sender['status'] == 201) {
            //     return response()->json(['status' => false, 'msg' => $sender['msg']], 201);
            // }
            // return response()->json(['status' => true, 'sender' => $sender], 200);


            // Delete sender from database

            $isExist = Newsletter::where('sender_id', $id)->get(['sender_id'])->first();
            $sender = 0;
            if (empty($isExist)) {
                $sender = Sender::with('user:id')->where('id', $id)->delete();
            }

            return response()->json(['status' => true, 'msg' => 'Deleted successfully', 'sender' => $sender], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
