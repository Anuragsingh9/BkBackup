<?php


namespace app\Services;
use App\User;
use App\Sender;
use Carbon\Carbon;
use DB;
use Config;
use Modules\Newsletter\Entities\Newsletter;
use Validator;
use Illuminate\Http\Request;

/**
 * Class SenderSingleton
 * @package Modules\Newsletter\Services
 */
class SenderSingleton
{
    /**
     * Make instance of sender singleton class
     * @return SenderSingleton|null
     */

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Get sender list from storage
     * @return mixed
     */
    // public function getSenderList()
    // {
    //     try{
    //         $sender = Sender::with('user:id')->paginate(config('newsletter.SENDER_LIST_PAGINATION_NUMBER'));
    //         return $sender;
    //     }catch(\Exception $e){
    //         $sender['status'] = 201;
    //         $sender['msg'] = $e->getMessage();
    //         return $sender;
    //     }

    // }

    /**
     * Search sender starting with 3 characters of his name in storage
     * @param Request $request
     * @return response
     */

    // public function getSearchDataForSender($name)
    // {
    //     try{
    //         // Search data of  sender using 3 characters
    //         $sender = User::with('user:id')->where('fname', 'LIKE', "%".$name."%")->get(['id','fname','lname','email','role']);
    //         return $sender;
    //     }catch(\Exception $e){
    //         $sender['status'] = 201;
    //         $sender['msg'] = $e->getMessage();
    //         return $sender;
    //     }
    // }

    /**
     * Add a sender in storage
     * @param $userId
     * @param $shortName
     * @param $fromName
     * @param $email
     * @param null $description
     * @return mixed
     */
    public function addSender($userId,$shortName,$fromName,$email,$description=null){
        try{

//            $validator = Validator::make($request->all(), [
//                'short_name' => array('required','string','min:2','max:10',
//                                        'regex:/^[\w-]*$/'),
//                'from_name' => 'required|string|min:2|max:255',
//                'email' => array('required','regex:/^.+@.+$/i')
//            ]);
//
//            if ($validator->fails()) {
//                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())]);
//            }

            //$id = User::where('email',$request->email)->pluck('id')->first();

            DB::connection('tenant')->beginTransaction();
            $insertData = ['user_id' => $userId,
                'short_name' => $shortName,
                'description' => $description,
                'from_name' => $fromName,
                'email' => $email
            ];
            $result = Sender::create($insertData);

            DB::connection('tenant')->commit();
            return $result;
        }
        
        catch(\Exception $e){
            DB::connection('tenant')->rollback();
            $sender['status'] = 500;
            $sender['msg'] = $e->getMessage();
            return $sender;
        }
    }

    /**
 

    /**
     * Update sender data in storage
     * @param $id
     * @param $userId
     * @param $shortName
     * @param $fromName
     * @param $email
     * @param null $description
     * @return response
     */
    public function updateSender($id,$userId,$shortName,$fromName,$email,$description=null){
        try{
            // Update sender in DB
            DB::connection('tenant')->beginTransaction();
            Sender::where('id', $id)->update(['user_id' => $userId,
                'short_name' => $shortName,
                'description' => $description,
                'from_name' => $fromName,
                'email' => $email
                ]);
            $sender = Sender::with('user:id')->find($id);
            DB::connection('tenant')->commit();
            return $sender;
        }catch(\Exception $e){
            DB::connection('tenant')->rollback();
            $sender['status'] = 500;
            $sender['msg'] = $e->getMessage();
            return $sender;
        }
    }

  

}



