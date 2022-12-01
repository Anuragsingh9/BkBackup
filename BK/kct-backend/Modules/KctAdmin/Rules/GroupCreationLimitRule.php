<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class GroupCreationLimitRule implements Rule {
    use ServicesAndRepo;
    private $errorMsg;

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
        $accountSettings = $this->adminRepo()->settingRepository->getAccountSetting();
        $maxGroupAllowed = $accountSettings['max_group_limit'] + 1;
        $createdGroupsCount = $this->adminRepo()->groupRepository->fetchAllGroups()->count();
        if ($createdGroupsCount >= $maxGroupAllowed){
            $this->errorMsg = __('kctadmin::messages.max_group_create_limit');
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->errorMsg;
    }


}
