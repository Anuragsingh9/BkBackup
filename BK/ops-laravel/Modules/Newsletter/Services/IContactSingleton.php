<?php

namespace Modules\Newsletter\Services;

use iContact\iContactApi;
use Modules\Newsletter\Entities\IContact;
use Modules\Newsletter\Entities\Newsletter;

class IContactSingleton
{

    /**
     * Make instance of iContact singleton class
     * @return IContactSingleton|null
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
     * Make instance of iContact API package class
     * @return iContactApi
     */

    public static function getIContactInstance()
    {
        iContactApi::getInstance()->setConfig(array(
           'appId' => config('constants.ICONTACT_API_APP_ID'),
            'apiPassword' => config('constants.ICONTACT_API_PASSWORD'),
            'apiUsername' => config('constants.ICONTACT_API_USERNAME')
        ));
        // iContactApi::getInstance()->setConfig(array(
        //    'appId' => '7a1769e120599aa78dc0df6745723c41',
        //     'apiPassword' => 'sv2IwpaxFEqWDAczJKjXfldS',
        //     'apiUsername' => 'anuraj@pebibits.com'
        // ));
        $oiContact = iContactApi::getInstance();
        return $oiContact;
    }

    /**
     * Add and update sender data to iContact
     * @param $sender
     * @param null $id
     * @return mixed
     */
    public function addSenderToIContact($sender, $id = null)
    {
        try {
            if ($id == null) {
                $url = 'https://app.icontact.com/icp/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/campaigns/';
                $postData = [[
                    'name' => $sender->short_name,
                    'description' => $sender->description,
                    'fromEmail' => $sender->email,
                    'fromName' => $sender->from_name,
                    'forwardToFriend' => 3,
                    'clickTrackMode' => 1,
                    'subscriptionManagement' => 1,
                    'useAccountAddress' => 1,
                    'street' => $sender->address,
                    'city' => $sender->city,
                    'state' => substr($sender->state,0,2),
                    'zip' => $sender->postal,
                    'country' => substr($sender->country,0,3),
                    'archiveByDefault' => 1
                ]];
            } else {
                $iContactId = IContact::where('column_id', $id)->where('type', 0)->first(['icontact_id']);
                $url = 'https://app.icontact.com/icp/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/campaigns/' . $iContactId->icontact_id;
                $postData = [
                    'name' => $sender->short_name,
                    'description' => $sender->description,
                    'fromEmail' => $sender->email,
                    'fromName' => $sender->from_name,
                    'forwardToFriend' => 3,
                    'clickTrackMode' => 1,
                    'subscriptionManagement' => 1,
                    'useAccountAddress' => 1,
                    'street' => $sender->address,
                    'city' => $sender->city,
                    'state' => substr($sender->state,0,2),
                    'zip' => $sender->postal,
                    'country' => substr($sender->country,0,3),
                    'archiveByDefault' => 1
                ];

            }
            $postData = json_encode($postData);
            $data = $this->postData($postData, $url);
            // var_dump($data,$postData);die;
            return $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * Update newsletter data in iContact
     * @param $newsletter
     * @param $id
     * @return mixed|string
     */

    public function updateNewsletterInIcontact($newsletter, $id)
    {
        try {

        $iContactMessageId = IContact::where('column_id',$id)->where('type',3)->first(['icontact_id']);
        $senderId = Newsletter::with('sender:id,short_name,from_name,email')->where('id',$id)->first(['sender_id']);
        $iContactSenderId = IContact::where('column_id',$senderId->sender_id)->first(['icontact_id']);
        $url = 'https://app.icontact.com/icp/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/'.$iContactMessageId->icontact_id;
        $postData = [
            'messageName' => $newsletter->name,
            'subject' => $newsletter->short_name,
            'campaignId' => $iContactSenderId->icontact_id,
            'htmlBody' => $newsletter->html_code
        ];
            $postData = json_encode($postData);
            $data = $this->postData($postData, $url);
            return $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Post sender data using curl request
     * @param $postData
     * @param $url
     * @return mixed
     */
    public function postData($postData,$url){
        $ch = curl_init();
         $appId = config('constants.ICONTACT_API_APP_ID');
        $username = config('constants.ICONTACT_API_USERNAME');
        $password = config('constants.ICONTACT_API_PASSWORD');
        $headers = array(
            'Except:',
            'Accept:  application/json',
            'Content-type:  application/json',
            'Api-Version:  2.2',
            'Api-AppId:  ' . $appId,
            'Api-Username:  ' . $username,
            'Api-Password:  ' . $password
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        return $result;
    }
    public function addcustomfieldsToIContact($customfields)
    {
        try {
            // if($id == null){
//                dd($customfields);
                $url = 'https://app.icontact.com/icp/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/customfields/';
                $postData = [[
                    'privateName' => $customfields['privateName'],
                    'displayToUser' => $customfields['displayToUser'],
                    'fieldType' => $customfields['fieldType'],
                ]];
            // }
            // else{
            //     $iContactId = IContact::where('column_id',$id)->where('type',0)->first(['icontact_id']);
            //     $url = 'https://app.icontact.com/icp/a/' . env('ICONTACT_ACCOUNT_ID') . '/c/' . env('ICONTACT_CLIENT_FOLDER_ID') . '/campaigns/'.$iContactId->icontact_id;
            //     $postData = [
            //         'name' => $sender->short_name,
            //         'description' => $sender->description,
            //         'fromEmail' => $sender->email,
            //         'fromName' => $sender->from_name,
            //         'forwardToFriend' => 3,
            //         'clickTrackMode' => 1,
            //         'subscriptionManagement' => 1,
            //         'useAccountAddress' => 1,
            //         'street' => '',
            //         'city' => '',
            //         'state' => '',
            //         'archiveByDefault' => 1
            //     ];

            // }
            $postData = json_encode($postData);
            $data = $this->postData($postData, $url);
            return $data;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

     /**
    * get Statistics of Messages
    * we use $messageId for determine that which message want to cancel
    * @param integer $messageId
    * @return objects
    */
    public function getStatistics($messageId){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/'.$messageId.'/statistics';
        // return response()->json($Iinstance->makeCall($sResource,'Get'));
        return $Iinstance->makeCall($sResource,'Get');
    }
    /**   * cancel Scheduled Messages
    * we use $sendId for determine that which message want to cancel
    * @param integer $sendId
    * @return boolean
    */
    public function cancelMessage($sendId){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/sends/'.$sendId;
        return response()->json(['status'=>$Iinstance->makeCall($sResource,'DELETE')]);
    }
    /**
	 * This method sends a message
     * $includeListIds can be integers, comma separated list of valid listIds
     * which want to add in message. it is required field.
     * $sExcludeListIds can be integers, comma separated list of valid listIds
     * $sExcludeSegmentIds can be integers, comma separated list of valid listIds
     * $sIncludeSegmentIds can be integers, comma separated list of valid listIds
     * $sScheduledTime can be Timestamp (in exactly YYYY-MM-DDTHH:MM::SS-04:00 format, Eastern Time)
     * Indicates the date and time on which an email will be sent.
	 * @access public
	 * @param string $includeListIds
	 * @param integer $messageId
	 * @param string [$sExcludeListIds] (optional)
	 * @param string [$sExcludeSegmentIds] (optional)
	 * @param string [$sIncludeSegmentIds] (optional)
	 * @param string [$sScheduledTime] (optional)
	 * @return object
	**/
    public function sendMessage($postData){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/sends';
        // $postData=[[
        //     'messageId'         =>  $messgeId,
        //     // 'excludeListIds'    => '',
        //     'includeListIds'    => '14887',
        //     // 'scheduledTime'     => (empty($sScheduledTime) ? null : date('c', strtotime($sScheduledTime)))
        //     'scheduledTime'     =>   date('c', strtotime('2019-05-28 19:00:13'))
        // ]];
        // return response()->json($Iinstance->makeCall($sResource,'POST',$postData));
         return $Iinstance->makeCall($sResource,'POST',$postData);
    }
    /**
	 * This method is use to delete Templete
     * $messageId can be integers
     * which want to delete in templete. it is required field.
     * @access public
	 * @param integer $messageId
	 * @return boolean
	**/
    public function deleteTemplete($messageId){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' .config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/'.$messageId;
        return response()->json($Iinstance->makeCall($sResource,'DELETE'));
    }
    /**
	 * This method update a message to
	 * your iContact API account
	 * @access public
	 * @param integer $messageId
	 * @param string $sSubject
	 * @param integer $iCampaignId
	 * @param string [$sHtmlBody] (optional)
	 * @param string [$sTextBody] (optional)
	 * @param string [$sMessageName]
	 * @param integer [$iListId] (optional)
	 * @param string [$sMessageType]
	 * @return object
	**/
    public function updateTemplete($messageId,$postData){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' .config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/'.$messageId;
        // $postData=[
        //     'campaignId'=>'9597',  //	(Sender Id)Indicates the sender property from which the message will pull its sending information.
        //     'messageType'=>'normal', // message type (normal, autoresponder, welcome and confirmation)
        //     'subject'=>'test api update', // subject of email
        //     'htmlBody'=>'<p>test api html data</p>',// html body of templete
        //     'messageName'=>'testApi update', // message name
        // ];
        return response()->json($Iinstance->makeCall($sResource,'POST',$postData));
    }
    /**
	 * addTempletes method adds a message to
	 * your iContact API account
	 * @access public
	 * @param string $subject
	 * @param integer $campaignId
	 * @param string [$htmlBody] (optional)
	 * @param string [$TextBody] (optional)
	 * @param string [$MessageName]
	 * @param integer [$iListId] (optional)
	 * @param string [$messageType]
	 * @return object
	**/
    public function addTempletes($postData){
        
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages';
        // $postData=[[
        //     'campaignId'=>'9597',  //	(Sender Id)Indicates the sender property from which the message will pull its sending information.
        //     'messageType'=>'normal', // message type (normal, autoresponder, welcome and confirmation)
        //     'subject'=>'test api ', // subject of email
        //     'htmlBody'=>'<p>test api html data</p>',// html body of templete
        //     'messageName'=>'testApi', // message name
        // ]];
        // return response()->json($Iinstance->makeCall($sResource,'POST',$postData));
         return $Iinstance->makeCall($sResource,'POST',$postData);
    }
    public function getTempletes(){

        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' .config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID'). '/messages';
        return response()->json($Iinstance->makeCall($sResource,'GET'));
    }
    public function getSubscribers(){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' .config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID'). '/subscriptions';
        return $Iinstance->makeCall($sResource,'GET');
    }
    /**
	 * updateSubscriber method is update subscriber status
	 * your iContact API account
     * $subscriptionId is subsriber id
     * $status is define status of subscriber (normal, unsubscribed)
	 * @access public
	 * @param string $status
	 * @param integer $subscriptionId
	 * @return object
	**/
    public function updateSubscriber($subscriptionId,$postData){
        // dd($subscriptionId,$postData);
        $Iinstance =   IContactSingleton::getIContactInstance();
        // $postData=[
        //       "status"=>"unsubscribed"
        // ];
        $sResource='/a/' .config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID'). '/subscriptions/'.$subscriptionId;
        return response()->json($Iinstance->makeCall($sResource,'POST',$postData));
    }
    /**
	 * addSubscriber method is add contact to list which is called subscription
	 * your iContact API account
     * $listId is id of list(icontact list)
     * $contactId is id of contactid
     * $status is define status of subscriber (normal, pending, unsubscribed)
	 * @access public
	 * @param integer $listId
	 * @param integer $contactId
	 * @param string $status
	 * @return object
	**/
    public function addSubscriber($postData){
        $Iinstance =   IContactSingleton::getIContactInstance();
        // $postData=[
        //    [
        //       "listId"=>"14887",
        //       "contactId"=>"29756068",
        //       "status"=>"normal"
        //     ],
        //    [
        //       "listId"=>"14887",
        //       "contactId"=>"29756069",
        //       "status"=>"normal"
        //     ],
        // ];
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/subscriptions';
        return ($Iinstance->makeCall($sResource,'POST',$postData));
    }
    /**
	 * deleteList method is delete list in icontact
	 * your iContact API account
     * $listId is id of list(icontact list)
	 * @access public
	 * @param integer $listId
	 * @return object
	**/
    public function deleteList($listid){
        $Iinstance =   IContactSingleton::getIContactInstance();
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/lists/'.$listid;
        return response()->json($Iinstance->makeCall($sResource,'DELETE'));
    }
    /**
	 * updateList method is update list in icontact
	 * your iContact API account
     * $postData is array of feild which you want to update
     * $postData=['name','welcomeMessageId','emailOwnerOnChange','welcomeOnManualAdd','welcomeOnSignupAdd','description','publicname',
	 * @access public
	 * @param string $name Required
	 * @param integer $listid (Required)
	 * @param integer $welcomeMessageId (Optional)
	 * @param bool [$emailOwnerOnChange] (Optional)
	 * @param bool [$welcomeOnManualAdd] (Optional)
	 * @param bool [$welcomeOnSignupAdd] (Optional)
	 * @param string [$description] (Optional)
	 * @param string [$publicName] (Optional)
	 * @return object
	**/
    public function updateList($listid,$postData){
        $Iinstance =   IContactSingleton::getIContactInstance();
        // $postData=[
        //         "name"=>'first List update'
        //     ];
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/lists/'.$listid;
        return ($Iinstance->makeCall($sResource,'POST', $postData));
    }
    /**
	 * addList method is add list in icontact
	 * your iContact API account
     * $postData is array of feild which you want to add.
     * it can be multiple array for multiple entry
     * $postData=['name','welcomeMessageId','emailOwnerOnChange','welcomeOnManualAdd','welcomeOnSignupAdd','description','publicname',
	 * @access public
	 * @param string $name Required
	 * @param integer $welcomeMessageId (Optional)
	 * @param bool [$emailOwnerOnChange] (Optional)
	 * @param bool [$welcomeOnManualAdd] (Optional)
	 * @param bool [$welcomeOnSignupAdd] (Optional)
	 * @param string [$description] (Optional)
	 * @param string [$publicName] (Optional)
	 * @return object
	**/
    public function addList($postData){

        $Iinstance =   IContactSingleton::getIContactInstance();
        // $postData=[
        //     [
        //         "name"=>'first List'
        //     ]
        //     ];
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/lists';
        // $sResource='/a/' . '1786048' . '/c/' . '18189' . '/lists';
        return ($Iinstance->makeCall($sResource,'POST', $postData));
    }
    public function getLists(){
        $Iinstance =   IContactSingleton::getIContactInstance();
        return response()->json($Iinstance->getLists());
    }
    public function getSender(){
        $Iinstance =   IContactSingleton::getIContactInstance();
//        dd($Iinstance->makeCall("/a/". config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') ."/campaigns", 'GET'));
    }
    // create contact function for multiple contact create
    /**
	 * The createContactis method adds a contact to your iContact account
     * $test2 is custom field which i create in custom field api
	 * @access public
	 * @param string $email Required
	 * @param string [$status] (Optional)
	 * @param string [$prefix] (Optional)
	 * @param string [$firstName] (Optional)
	 * @param string [$lastName] (Optional)
	 * @param string [$suffix] (Optional)
	 * @param string [$street] (Optional)
	 * @param string [$street2] (Optional)
	 * @param string [$city] (Optional)
	 * @param string [$state] (Optional)
	 * @param string [$postalCode] (Optional)
	 * @param string [$phone] (Optional)
	 * @param string [$fax] (Optional)
	 * @param string [$business] (Optional)
	 *
	 * @return object
	**/
    public function createContact( $aContact){
        $Iinstance =   IContactSingleton::getIContactInstance();
        // $aContact=[[
        //     'email'=>'test@mailsac.com',
        //     'firstName'=>'test',
        //     'lastName'=>'test',
        //     'phone'=>'1234567890',
        //     'test2'=>'1'
        // ],
        // [
        //     'email'=>'test1@mailsac.com',
        //     'firstName'=>'test1',
        //     'lastName'=>'test1',
        //     'phone'=>'',
        //     'test2'=>'2'
        // ]];

        return ($Iinstance->makeCall("/a/". config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') ."/contacts", 'POST', $aContact));
    }
    //single contact update function
    /**
	 * The createContactis method adds a contact to your iContact account
     * $contactID is id of contact.
     * $test2 is custom field which i create in custom field api
	 * @access public
	 * @param integer $contactID Required
	 * @param string $email  Required
	 * @param string [$status (Optional)
	 * @param string [$prefix (Optional)
	 * @param string [$firstName (Optional)
	 * @param string [$lastName (Optional)
	 * @param string [$suffix (Optional)
	 * @param string [$street (Optional)
	 * @param string [$street2 (Optional)
	 * @param string [$city (Optional)
	 * @param string [$state (Optional)
	 * @param string [$postalCode (Optional)
	 * @param string [$phone (Optional)
	 * @param string [$fax (Optional)
	 * @param string [$business (Optional)
	 *
	 * @return object
	**/
    public function updateContact($contactID,$aContact){
        $Iinstance =   IContactSingleton::getIContactInstance();
        // $aContact=[];
        // $aContact['firstName']='test';
        // $aContact['test2']='123';
        return response()->json($Iinstance->makeCall("/a/". config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') ."/contacts/".$contactID, 'POST', $aContact, 'contact'));
    }
    //single contact delete function
    /**
	 * The deleteContact is method delete a contact to your iContact account
     * $contactID is id of contact.
     * @access public
	 * @param integer $contactID Required
	 * @return boolean
	**/
    public function deleteContact($contactID){
        $Iinstance =   IContactSingleton::getIContactInstance();
        return response()->json($Iinstance->makeCall("/a/". config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') ."/contacts/".$contactID, 'DELETE'));
    }


    //get single and all contact function
    /**
	 * The getContact is method get all or specific a contact to your iContact account
     * $contactID is id of contact.
     * @access public
	 * @param integer $contactID
	 * @return boolean
	**/
    public function getContact($contactID=null){
        $Iinstance =   IContactSingleton::getIContactInstance();
        if($contactID==null){
            return response()->json($Iinstance->makeCall("/a/". config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') ."/contacts/", 'GET'));
        }
        else{
        return response()->json($Iinstance->makeCall("/a/". config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') ."/contacts/".$contactID, 'GET'));
        }
    }


    /**
	 * The createCustomeField method adds a custom field in your iContact contact
     * $fieldType is type of field it can be one of the following: (checkbox, text, number, decimalOne, decimalTwo, decimalThree, decimalFour, date)
     * $displayToUser 1 to display the field or 0 to hide the field
     * $privateName is type of field it can be one of the following: (checkbox, text, number, decimalOne, decimalTwo, decimalThree, decimalFour, date)
	 * @access public
	 * @param string [$privateName] Required
	 * @param string [$displayToUser] Required
	 * @param string [$fieldType] Required
	 *
	 * @return object
	**/
    public function createCustomeField($postData){
         $Iinstance =   IContactSingleton::getIContactInstance();
        //  $postData = [[
        //     'privateName' => 'opsId',
        //     'displayToUser' => 1,
        //     'fieldType' => 'text',
        // ]];
        $sResource='/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/customfields/';
       return response()->json( $Iinstance->makeCall($sResource,'POST', $postData));
    }
    public function spamTest($postData){
        $Iinstance =   IContactSingleton::getIContactInstance();
    //     $postData = [[
    //         'messageId'=>55130,
    //         'includeListIds'=>'23342,23343'
    //    ]];
       $sResource='/a/' .config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/sends/';
      return $Iinstance->makeCall($sResource,'POST', $postData);
   }
   public function getBounces($messageId)
    {
        $Iinstance = IContactSingleton::getIContactInstance();
        $sResource = '/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/' . $messageId . '/bounces';
        return $Iinstance->makeCall($sResource, 'Get');
    }
    public function getClicks($messageId)
    {
        $Iinstance = IContactSingleton::getIContactInstance();
        $sResource = '/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/' . $messageId . '/clicks';
        return $Iinstance->makeCall($sResource, 'Get');
    }
    public function getOpens($messageId)
    {
        $Iinstance = IContactSingleton::getIContactInstance();
        $sResource = '/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/' . $messageId . '/opens';
        return $Iinstance->makeCall($sResource, 'Get');
    }
    public function getUnsubscribes($messageId)
    {
        $Iinstance = IContactSingleton::getIContactInstance();
        $sResource = '/a/' . config('constants.ICONTACT_ACCOUNT_ID') . '/c/' . config('constants.ICONTACT_CLIENT_FOLDER_ID') . '/messages/' . $messageId . '/unsubscribes';
        return $Iinstance->makeCall($sResource, 'Get');
    }

}