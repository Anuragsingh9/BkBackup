<?php

namespace Modules\Crm\Rules;

use App\Model\Skill;
use App\Model\SkillTabs;
use Illuminate\Contracts\Validation\Rule;
use Modules\Crm\Entities\CrmFilterCondition;
use Modules\Crm\Entities\CrmFilterRule;
use Illuminate\Http\Request;
use Modules\Crm\Entities\CrmFilterType;

class CrmFilterFieldNameExist implements Rule
{

    protected $request;
    protected $message = 'Filter name not exist.';

    /**
     * Create a new rule instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        //
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $return = false;
        $attr = explode('.', $attribute);
        if(
            !empty($attr)
            && isset($attr[1])
            && is_numeric($attr[1])
            && isset($this->request->conditions[$attr[1]])
            && $this->request->filter_type_id
        ){
            $data = $this->request->conditions[$attr[1]];

            $filter = CrmFilterType::find($this->request->filter_type_id);
            $component = json_decode($filter->component);
            $comp = $data['component'];
            if(isset($data['is_default']) && isset($data['component']) && isset($component->$comp)){
                $component = $component->$comp;
                if($data['is_default'] == true){
                    $default = $this->getModelFillable($component->default, $data['component']);
                    $return = in_array($value, $default);
                }else if($data['is_default'] == false){
                    $custom = $this->getCustomFillable($component->custom, $data['component']);
                    $return = array_key_exists($data['field_id'], $custom);
                    if(!$return)
                        $this->message = 'field id not exist';
                }else{
                    $return = false;
                }
            }else{
                $return = false;
            }
        }

        return $return;
    }

    /**
     * get fillable fields from database table
     *
     * @param array $models
     * @return array of model fields
     */
    protected function getModelFillable(array $models, $key = '')
    {
        $fields = [];
        foreach ($models as $model){
            $object = new $model;

            if($key == 'user' || $key == 'contact'){
                $field = $object->getFillablesPerson();
            }else{
                $field = $object->getFillables();
            }

            if($key == 'persons' && !empty($fields)){
                $fields = array_intersect($fields, $field);
            }else{
                $fields = array_merge($fields, $field);
            }
        }
        return $fields;
    }

    /**
     * @param array $models
     * @param string $key
     * @return array
     */
    protected function getCustomFillable(array $models, $key = '')
    {
        $type = [
            'user' => [
                'index' => 0,
                'name' => 'User'
            ],
            'contact' => [
                'index' => 1,
                'name' => 'Contact'
            ],
            'company' => [
                'index' => 3,
                'name' => 'Company'
            ],
            'instance' => [
                'index' => 4,
                'name' => 'Instance'
            ],
            'union' => [
                'index' => 5,
                'name' => 'Union'
            ],
        ];
        $tabs = [];
        if(isset($type[$key])){
            $skillsTabs = SkillTabs::where('tab_type', $type[$key]['index'])->pluck('id');
            $tabs = Skill::whereIn('skill_tab_id', $skillsTabs)->pluck('name', 'id')->toArray();

        }
        return $tabs;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
