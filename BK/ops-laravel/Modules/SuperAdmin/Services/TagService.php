<?php


namespace Modules\SuperAdmin\Services;


use App\Services\Service;
use Modules\SuperAdmin\Entities\UserTag;

class TagService extends Service
{
    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Get professional tags
     * @return mixed
     */
    public function getProfessionalTags(){
        return  UserTag::where(function ($q){
            $q->where('status','pending');
            $q->where('tag_type','pro');
        })->get();
    }

    /**
     *  Get personal tags
     * @return mixed
     */
    public function getPersonalTags(){
        return  UserTag::where(function ($q){
            $q->where('status','pending');
            $q->where('tag_type','perso');
        })->get();
    }

    /**
     * Update tag details
     * @param $data
     * @param $status
     * @return UserTagResource
    */
    public function updateTag($data, $status) {
        $getTag = UserTag::where('id', $data->id)->first();

        $update = UserTag::where('id', $data->id)->update([
            'tag_EN' => $data->tag_en ? $data->tag_en : $getTag->tag_EN,
            'tag_FR' => $data->tag_fr ? $data->tag_fr : $getTag->tag_FR,
            'status' => $status ? $status : $getTag->status
        ]);

        if($data->tag_en || $data->tag_fr) {
            $getTag->tag_EN = $data->tag_en ? $data->tag_en : $getTag->tag_EN;
            $getTag->tag_FR = $data->tag_fr ? $data->tag_fr : $getTag->tag_FR;

            return ($update == 1) ? $getTag : false;
        } else {
            if($getTag->tag_type == 1)
                $unmoderatedTags = $this->getProfessionalTags();
            elseif($getTag->tag_type == 2)
                $unmoderatedTags = $this->getPersonalTags();
        }

        return ($update == 1) ? $unmoderatedTags : false;
    }
}