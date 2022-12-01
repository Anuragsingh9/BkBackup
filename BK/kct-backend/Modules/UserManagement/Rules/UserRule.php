<?php

namespace Modules\UserManagement\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Traits\UmHelper;

class UserRule implements Rule {

    use UmHelper;
    use ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;
    private string $error = '';
    private ?string $attribute;
    private $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attribute = null) {
        $this->attribute = $attribute;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $this->value = $value;
        if (!$this->value) {
            return true;
        }
        if (!$this->attribute) {
            $exp = $this->setAttribute($attribute);
            $this->attribute = $attribute = $exp['attribute'];
        } else {
            $attribute = $this->attribute;
        }

        switch ($attribute) {
            case 'fname':
                return $this->firstNameValidation();
            case 'lname':
                return $this->lastNameValidation();
            case "email":
                return $this->emailValidation();
            case "password":
                return $this->passwordValidation();
            case "password_change":
                return $this->passwordChangeValidation();
            default:
                return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->error;
    }

    public function firstNameValidation(): bool {
        $val = config("usermanagement.modelConstants.users.validations");
        $atr = ['attribute' => $this->attribute];
        if (!is_string($this->value)) {
            $this->error = __("validation.string", $atr);
            return false;
        } else if (strlen($this->value) < $val['fname_min']) {
            $this->error = __('validation.min.numeric', array_merge($atr, ['min' => $val['fname_min']]));
            return false;
        }
        if (strlen($this->value) > $val['fname_max']) {
            $this->error = __('validation.max.numeric', array_merge($atr, ['max' => $val['fname_max']]));
            return false;
        }
        if (!preg_match("/^[a-zàâäáçéèêëìîïíôóòûùüúÿñæœÀÂÁÇÉÈÊËÎÏÌÍÔÛÙÜÚŸÑÆŒ¿ ̛̔̕'’-]*$/i", $this->value)) {
            $this->error = __("validation.alpha", $atr);
            return false;
        }
        return true;
    }

    public function lastNameValidation(): bool {
        $val = config("usermanagement.modelConstants.users.validations");
        $atr = ['attribute' => $this->attribute];
        if (!is_string($this->value)) {
            $this->error = __("validation.string", $atr);
            return false;
        } else if (strlen($this->value) < $val['lname_min']) {
            $this->error = __('validation.min.numeric', array_merge($atr, ['min' => $val['lname_min']]));
            return false;
        }
        if (strlen($this->value) > $val['lname_max']) {
            $this->error = __('validation.max.numeric', array_merge($atr, ['max' => $val['lname_max']]));
            return false;
        }
        if (!preg_match("/^[a-zàâäáçéèêëìîïíôóòûùüúÿñæœÀÂÁÇÉÈÊËÎÏÌÍÔÛÙÜÚŸÑÆŒ¿ ̛̔̕'’-]*$/i", $this->value)) {
            $this->error = __("validation.alpha", $atr);
            return false;
        }
        return true;
    }

    public function emailValidation(): bool {
        $validator = Validator::make(['email' => $this->value], [
            'email' => 'email',
        ]);
        if ($validator->fails()) {
            $this->error = __("validation.email", ['attribute' => $this->attribute]);
            return false;
        }
        if (request()->has('event_uuid')) {
            $event = $this->adminRepo()->eventRepository->findByEventUuid(request()->event_uuid);
            $allEventUsers = $event->eventUsers->pluck('id')->toArray();
            $currentUser = $this->adminServices()->userService->findByEmail($this->value);
            if ($currentUser){
                if (in_array($currentUser->id, $allEventUsers)) {
                    $this->error = __("usermanagement::messages.already_event_member", ['user' => $currentUser->fname . ' ' . $currentUser->lname]);
                    return false;
                }
            }
            return true;
        }
        return true;
    }

    public function passwordValidation(): bool {
        $val = config("usermanagement.modelConstants.users.validations");
        $atr = ['attribute' => $this->attribute];
        if (!is_string($this->value)) {
            $this->error = __("validation.string", $atr);
            return false;
        } else if (strlen($this->value) < $val['password_min']) {
            $this->error = __('validation.min.numeric', array_merge($atr, ['min' => $val['password_min']]));
            return false;
        }
        if (strlen($this->value) > $val['password_max']) {
            $this->error = __('validation.max.numeric', array_merge($atr, ['max' => $val['password_max']]));
            return false;
        }
        return true;
    }

    public function passwordChangeValidation(): bool {
        if($this->passwordValidation()){
            $user = $this->umRepo()->userRepository->findByEmail(request()->input('email'));
            if(!$user){
                return true;
            }
            if(Hash::check(request()->input('password'),$user->password) ) {
                $this->error = __('usermanagement::messages.password_should_not_old');
                return false;
            }
            else{
                return true;
            }
        }
        return false;
    }
}
