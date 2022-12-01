<?php

namespace Modules\Crm\Rules;

use App\Model\Skill;
use App\Model\SkillTabs;
use Illuminate\Contracts\Validation\Rule;
use Modules\Crm\Entities\CrmFilterCondition;
use Modules\Crm\Entities\CrmFilterRule;
use Illuminate\Http\Request;
use Modules\Crm\Entities\CrmFilterType;

class CrmFilterNameRequire implements Rule
{

    protected $request;
    protected $message = 'Field name not exist.';

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
//            dd($data);
            if(isset($data['is_default'])){
                if($data['is_default'] == true){
                    if(!isset($data['field_name'])){
                        $this->message = 'field name is required';
                        return false;
                    }
                    return true;
                }else if($data['is_default'] == false){
                    if(!isset($data['field_id'])){
                        $this->message = 'field id is required';
                        return false;
                    }
                    return true;

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
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
