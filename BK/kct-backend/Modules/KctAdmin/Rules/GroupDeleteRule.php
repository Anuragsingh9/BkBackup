<?php

namespace Modules\KctAdmin\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class GroupDeleteRule implements Rule
{
    use ServicesAndRepo;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value): bool {
        $defaultGroup = $this->adminRepo()->groupRepository->getDefaultGroup();
        if($value != $defaultGroup->group_key){
            return true;
        }
        else{
             $this->msg = __("kctadmin::messages.delete_super_group");
             return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
