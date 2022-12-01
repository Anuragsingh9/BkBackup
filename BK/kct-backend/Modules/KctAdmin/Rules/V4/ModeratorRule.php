<?php

namespace Modules\KctAdmin\Rules\V4;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class ModeratorRule implements Rule {

    use ServicesAndRepo;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        if(!in_array(request('event_broadcasting') , [1,2])) {
            return true;
        }
        $enabledSetting = $this->adminServices()->zoomService->getZoomSettings();

        if (!$enabledSetting) {
            $this->error = __('kctadmin::messages.zoom_account_expired');
            return false;
        }

        $key = request('event_broadcasting') == "2" ? 'webinar_data' : 'meeting_data';
        if (is_numeric($value) && (in_array($value, Arr::pluck($enabledSetting->setting_value[$key]['hosts'] ?? [], 'id')))) {
            return true;
        } else {
            $this->error = __('validation.exists', ['attribute' => 'moderator']);
            return false;
        }
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
