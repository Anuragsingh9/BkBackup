<?php

namespace App\Services;

use App\Model\ListModel;
use App\NewsletterList;

class ListServices
{
    protected static $instance;

    //DEFINING SINGLETON CLASS
    public static function getInstance()
    {
        if (is_null(ListServices::$instance)) {
            ListServices::$instance = new ListServices();
        }
        return ListServices::$instance;
    }

    /**
     * Store a newly created list in storage.
     * @param string $name
     * @param string $description
     * @param boolean $type
     * @param string $typology_id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addList($name, $description, $type, $typology_id,$creation_type)
    {
        //ADDING THE REQUESTED DATA IN DATABASE
        return $newsletter_list = ListModel::create([
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'typology_id' => $typology_id,
             'creation_type'=>$creation_type
        ]);
    }

    /**
     * Update the specified list in storage.
     *
     * @param string $name
     * @param string $description
     * @param boolean $type
     * @param string $typology_id
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateList($id, $name = null, $description = null, $type = null, $typology_id = null)
    {
        //UPDATING THE DATA IN THE DATABSE THROUGH ID
      return  $list = ListModel::whereId($id)->update([
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'typology_id' => $typology_id
        ]);

    }


    /**
     * This function is used for attaching Users/Contacts to
     * appropriate list
     * @param $list having data object of particular list
     * @param $attach contains array of users or contacts
     * @return mixed
     */
    public function attachList($list, $attach)
    {
        if ($list->type == 1 || $list->type == 4) {
            return $list->newsletter_contacts()->attach($attach);
        }
        return $list->users()->attach($attach);
    }
    public function detachList($list, $detach)
    {
        if ($list->type == 1 || $list->type == 4) {
            return $list->newsletter_contacts()->detach($detach);
        }
        return $list->users()->detach($detach);
    }
    /**
     * this functions is designed to get and create
     * an array of users for adding them in Icontact
     * @param $users contains array of users get form workshops
     * @return array
     */
    public function addContactWorkshops($users)
    {
        $ids = [];
        if (count($users) > 0) {
            foreach ($users as $user) {
                if (!empty($user->meta)) {
                    foreach ($user->meta as $item) {
                        if (!empty($item->user)) {
                            $ids[] = ['email' => $item->user->email, 'firstName' => $item->user->fname, 'lastName' => $item->user->lname, 'phone' => $item->user->phone, 'test3' => $item->user->id];
                        }
                    }
                }
            }
        }
        return $ids;
    }
}