<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class RemoveEventUserRule implements Rule
{
    private $event;
    private $error;
    use ServicesAndRepo;
    use KctHelper;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($event)
    {
       $this->event = $event;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $atr = ['attribute' => $attribute];
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->event);
//        $eventUsers = $event->eventUserRelation->pluck('user_id')->toArray();
        $moderators = $this->adminRepo()->eventRepository->getEventUsers($this->event, 'moderator')->pluck('user_id')->toArray();
        $speakers = $this->adminRepo()->eventRepository->getEventUsers($this->event, 'speaker')->pluck('user_id')->toArray();
        $hosts = $this->getSpaceHosts($event);
        if ($value == Auth::user()->id) {
            $this->error = __("kctadmin::messages.cannot_self_delete", $atr);
            return false;
        } elseif (in_array($value, $moderators)) {
            $this->error = __("kctadmin::messages.cannot_remove_moderator", $atr);
            return false;
        } elseif (in_array($value, $speakers)) {
            $this->error = __("kctadmin::messages.cannot_remove_speaker", $atr);
            return false;
        } elseif (in_array($value, $hosts)) {
            $this->error = __("kctadmin::messages.cannot_remove_SH", $atr);
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
        return $this->error;
    }
}
