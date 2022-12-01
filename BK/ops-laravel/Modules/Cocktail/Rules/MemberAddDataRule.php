<?php

namespace Modules\Cocktail\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class MemberAddDataRule implements Rule {
    /**
     * @var array|string|null
     */
    private $msg;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($value && $data = json_decode($value, 1)) {
            if (isset($data['id']) && $data['id']) {
                $user = User::find($data['id']);
                if(!$user) {
                    $this->msg = __('cocktail::message.user_not_exists');
                return false;
                }
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
        return $this->msg;
    }
}
