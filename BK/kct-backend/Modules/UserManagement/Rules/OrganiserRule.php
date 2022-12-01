<?php

namespace Modules\UserManagement\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Repositories\BaseRepository;
use Modules\UserManagement\Traits\ServicesAndRepo;


class OrganiserRule implements Rule {
    use ServicesAndRepo;
    use \Modules\KctAdmin\Traits\ServicesAndRepo;

    /**
     * @var array|string|null
     */
    private $error;
    private string $attribute;
    /**
     * @var mixed
     */
    private $value;
    private $email;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($email) {
        $this->email = $email;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $this->attribute = $attribute;
        $this->value = $value;
        switch ($attribute) {
            case 'email':
                return $this->validateOrganiserEmail();
            case 'password':
                return $this->validatePassword();
            default:
                return true;
        }
    }

    public function validateOrganiserEmail(): bool {
        $validator = Validator::make(['email' => $this->value], [
            'email' => \Illuminate\Validation\Rule::exists('tenant.users')->whereNull('deleted_at'),
        ]);
        if ($validator->fails()) {
            $this->error = __('usermanagement::messages.email_not_exist');;
            return false;
        }
        return true;
    }

    public function validatePassword(): bool {
        $user = $this->umRepo()->userRepository->findByEmail($this->email);
        if ($user){
            $isOrganiser = $this->adminRepo()->groupUserRepository->isOrganiser($user->id);
            if (!Hash::check($this->value, $user->password)) {
                $this->error = __("validation.exists", ['attribute' => $this->attribute]);
                return false;
            } elseif (!$isOrganiser) {
                $this->error = __('usermanagement::messages.only_organiser_can_access');
                return false;
            } else{
                return true;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->error;
    }
}
