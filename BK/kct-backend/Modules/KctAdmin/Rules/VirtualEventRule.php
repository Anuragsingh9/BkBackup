<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Repositories\IEventRepository;

class VirtualEventRule implements Rule {

    private string $msg;

    private IEventRepository $eventRepository;


    public function __construct() {
        $this->eventRepository = app(IEventRepository::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        // $event = Event::where('event_uuid', $value)->where('type', config('kctadmin.event_type.virtual'))->first();
        $event = $this->eventRepository->findByEventUuid($value);
        // $conference = KctConference::where('event_uuid', $value)->first();
        $conference = $this->kctConferenceRepository->findByEventUuid($value);
        if ($event && isset($conference->conference_settings['event_uses_bluejeans_event'])) {
            if ($conference->conference_settings['event_uses_bluejeans_event'] != 0) {
                $this->msg = __('kctadmin::messages.event_must_not_bj');
                return false;
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
