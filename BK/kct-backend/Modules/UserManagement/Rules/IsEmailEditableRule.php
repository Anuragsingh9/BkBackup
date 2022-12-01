<?php

namespace Modules\UserManagement\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Traits\ServicesAndRepo;

class IsEmailEditableRule implements Rule
{
    use ServicesAndRepo;

    /**
     * @var array|string|null
     */
    private $error;
    private $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
        $user = $this->umRepo()->userRepository->findById($this->id);
        if ($user){
            $hasChangedPassword = $user->login_count;
            $isRequiredField = $hasChangedPassword ? 'nullable' : 'required';
            if ($hasChangedPassword){
                if ($user->email != $value){
                    $this->error = __('usermanagement::messages.cannot_update_email');
                    return false;
                }
                return true;
            }
            $validator = Validator::make(['email' => $value],[
                'email' => "$isRequiredField",
            ]);
            if ($validator->fails()){
                $this->error = __("validation.required");
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error;
    }
}
