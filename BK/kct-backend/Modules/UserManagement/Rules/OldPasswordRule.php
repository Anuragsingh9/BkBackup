<?php

namespace Modules\UserManagement\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Traits\ServicesAndRepo;

class OldPasswordRule implements Rule
{
    use ServicesAndRepo;

    private $email;
    private $oldPassword;
    /**
     * @var array|string|null
     */
    private $error;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $user = $this->umRepo()->userRepository->findByEmail($this->email);
        if ($user){
           if (Hash::check($value, $user->password)){
               $this->error = __('usermanagement::messages.cannot_use_old_pwd');
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
