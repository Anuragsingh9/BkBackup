<?php

namespace Modules\Newsletter\Services;
use Modules\Newsletter\Entities\Contact;
use App\Entity;
use App\EntityUser;
use App\Model\EntityDependency;
use DB;
class ContactServices
{
    protected static $instance;
    //DEFINING SINGLETON CLASS
    public static function getInstance()
    {
        if (is_null(ContactServices::$instance)) {
            ContactServices::$instance = new ContactServices();
        }
        return ContactServices::$instance;
    }
    
    /**
     * Store a newly created resource in storage.
     * @param string $fname
     * @param string $lname
     * @param string $email
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addContact($email,$fname=null,$lname=null,$company=null,$instance=null,$union=null){
        //ADDING THE REQUESTED DATA IN DATABASE

        $contact = Contact::create([

        'fname' =>  $fname,
        'lname' =>  $lname,
        'email' =>  $email,
        ]);  
        if($company!=null)
        $this->addEtity($company,$contact->id,'company');
        if($instance!=null){
                $this->addEtity($instance,$contact->id,'instance');}
        if($union!=null)
        $this->addEtity($union,$contact->id,'union');
        
        return $contact; 
    }
    public function addEtity($val,$id,$type){
        if ($type == 'union')
            $typeid = 3;
        elseif ($type == 'instance')
            $typeid = 1;            
        elseif ($type == 'company')
            $typeid = 2;

            $entity = Entity::where(function ($q) use ($val) {
                $q->orWhere('long_name', 'LIKE', '%' . $val . '%');
                $q->orWhere('short_name', 'LIKE', '%' . $val . '%');
                $q->orWhere(DB::raw("CONCAT(`long_name`, ' ', `short_name`)"), 'LIKE', '%' . $val . "%");
                $q->orWhere('entity_description', 'LIKE', '%' . $val . '%');
            })->where('entity_type_id', $typeid)->first(['id', 'long_name','short_name']);
            if(!$entity){

                $entity=Entity::create(['long_name'=>$val,'short_name'=>$val,'entity_type_id'=>$typeid]);
            }
        if ($type != 'union') {

            $entityUserCount = EntityUser::where(['contact_id' => $id, 'entity_id' => $entity->id])->count();
                 // dd('sadsa',$entity);
            if ((($entityUserCount == 0) && ($type == ('union' || 'company')))) {
                $entityUser = EntityUser::create(['contact_id' => $id, 'entity_label' => '', 'entity_id' => $entity->id]);
                return response()->json(['status' => true, 'data' => $entityUser], 200);
            } else {
                return response()->json(['status' => false, 'msg' => ucfirst($type) . ' belongs to another ' . ucfirst($type)], 422);
            }
        } else {
            $entityUser = EntityUser::create(['contact_id' => $id, 'entity_label' => '', 'entity_id' => $entity->id]);
        }
        return response()->json(['status' => true, 'data' => $entityUser], 200);
   
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  string $fname
     * @param  string $lname
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateContact($id=0,$fname=null,$lname=null){
        //UPDATING THE DATA IN THE DATABSE THROUGH ID
        $contact= Contact::whereId($id)->update([
            'fname' => $fname,
            'lname' => $lname,
        ]);
        return response()->json(['status'=>true,'data'=>$contact,'msg'=> 'Contact Updated Successfully!'],200);
    }
}