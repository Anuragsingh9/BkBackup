<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class LiveEventRule implements Rule {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    private $error;
    private $eventUuid;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($eventUuid) {
        $this->eventUuid = $eventUuid;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {

        switch ($attribute){
            case 'event_uuid':
                return $this->validateEventUuid($attribute);
//            case 'event_live_image':
//                return $this->validateLiveEventImage($attribute);
//            case 'event_live_video_link':
//                return $this->validateLiveEventVideo($attribute);
            default:
                return true;
        }
    }

    public function validateEventUuid($attribute): bool {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->eventUuid);
        if ($this->isPastEvent($event)){
            $this->error = __('kctadmin::messages.is_past_event');
            return false;
        }elseif(!isset($event->event_settings['is_auto_key_moment_event']) || !$event->event_settings['is_auto_key_moment_event'] == 1 ){
            $this->error = __('kctadmin::messages.not_live_event_type');
            return false;
        }elseif (!$event) {
            $this->error = __("validation.exists", ['attribute' => $attribute]);
            return false;
        }
        return true;
    }

    public function validateLiveEventImage($attribute): bool {
        $liveEventValidation = config('kctadmin.modelConstants.event_live_image');
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->eventUuid);
        if (isset($event->event_settings['event_images'])) {
            $imagesCount = count($event->event_settings['event_images']);
            if ($imagesCount >= $liveEventValidation['max_image_limit']) {
                $this->error = __('validation.max.numeric', ['attribute' => $attribute, 'max' => $liveEventValidation['max_image_limit']]);
                return false;
            }
        }
        return true;
    }

    public function validateLiveEventVideo($attribute): bool {
        $liveEventValidation = config('kctadmin.modelConstants.event_live_image');
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->eventUuid);
        if (isset($event->event_settings['event_video_links'])) {
            $videoCount = count($event->event_settings['event_video_links']);
            if ($videoCount >= $liveEventValidation['max_video_limit']) {
                $this->error = __('validation.max.numeric', ['attribute' => $attribute, 'max' => $liveEventValidation['max_video_limit']]);
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
        return $this->error;
    }
}
