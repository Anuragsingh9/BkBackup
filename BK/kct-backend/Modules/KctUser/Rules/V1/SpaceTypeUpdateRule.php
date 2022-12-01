<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctUser\Entities\EventSpace;
use Modules\KctUser\Services\KctUserEventService;

class SpaceTypeUpdateRule implements Rule
{
    private $space;
    private $msg;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($space_uuid)
    {
        $this->space = EventSpace::where('space_uuid',$space_uuid)->first();
    }

    /**
     *  @description to validate the space type for space update
     * 1. Regular to VIP will pass the validation
     * 2. VIP to Regular  will pass the validation
     * 3. DUO to any space type will fail the  validation
     * 4. Default space is always Regular
     *
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Checking weather it is default space
        // If default space show validation(we cannot make changes in default space)
        $isPassDefaultValidation = $this->validateDefaultSpace($this->space->event_uuid,$value);
        if ($isPassDefaultValidation){
                $this->msg  = __('kctuser::message.default_not_possible');
                return false;
        }
        // Getting the space type of given space
        $currentSpaceType = $this->getSpaceType($this->space);
        switch ($currentSpaceType){
            case config('kctuser.default.space_type_vip'):
                $error =  $this->validateVip($value);
                break;
                case config('kctuser.default.space_type_duo'):
                $error =  $this->validateDuo($value);
                break;
                case 0:
                $error = $this->validateRegular($value);
                break;
            default :
                return true;
        }
        // if any validation error found show the error
        if ($error){
            $this->msg = $error;
                return false;
        }
                return true;
    }

    public function validateDefaultSpace($space_uuid,$value){
        $defaultSpace = KctUserEventService::getInstance()->getEventDefaultSpace($space_uuid);
        // Checking if space is default space
        if($defaultSpace->space_uuid === $this->space->space_uuid){
            // It will show validation error as space is default space
            if ($value != 0){
                return __('kctuser::message.default_not_possible');
            }
                return "";
        }
    }

    /**
     * @param $value
     * @return array|string|null
     */
    public function validateVip($value){
        if($value == 2){
            // It will show validation error as we cannot change vip to duo
            return __('kctuser::message.vip_to_duo_not_possible');
        }
            return "";
    }

    /**
     * @param $value
     * @return array|string|null
     */
    public function validateDuo($value){
        if($value != 2){
            // It will show validation error as duo cannot be change to any other space
            return __('kctuser::message.duo_change_not_possible');
        }
            return "";
    }

    /**
     * @param $value
     * @return array|string|null
     */
    public function validateRegular($value){
        if($value == 2){
            // It will show validation error as we cannot make regular to duo
            return __('kctuser::message.regular_to_duo_not_possible');
        }
            return "";
    }

    /**
     * Get the space type
     * @param $space
     * @return int
     */
    public function getSpaceType($space){
        if ($space->is_vip_space){
            $spaceType = 1;
        }elseif ($space->is_duo_space){
            $spaceType = 2;
        }else{
            $spaceType = 0;
        }
        return $spaceType;
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
