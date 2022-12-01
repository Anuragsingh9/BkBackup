<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Events\Service\ValidationService;

class DataService extends Service {
    
    /**
     * To prepare the keepContact data to be filled from request so proper data in proper manner saved
     * @param $request
     * @return array
     */
    public function prepareKeepContactCustomization($request) {
        return [
            'keepContact' => [
                'page_customisation' => [
                    'keepContact_page_title'       => $request->keepContact_page_title,
                    'keepContact_page_description' => $request->keepContact_page_description,
                    'website_page_link'            => $request->website_page_link,
                    'twitter_page_link'            => $request->twitter_page_link,
                    'linkedIn_page_link'           => $request->linkedIn_page_link,
                    'facebook_page_link'           => $request->facebook_page_link,
                    'instagram_page_link'          => $request->instagram_page_link,
                ],
                'graphics_setting'   => [
                    'hover_border_color'                     => ['color' => json_decode($request->hover_border_color, true)['transparency']],
                    'main_background_color'                  => ['color' => json_decode($request->main_background_color, true)['transparency']],
                    'texts_color'                            => ['color' => json_decode($request->texts_color, true)['transparency']],
                    'keepContact_color_1'                    => ['color' => json_decode($request->keepContact_color_1, true)['transparency']],
                    'keepContact_color_2'                    => ['color' => json_decode($request->keepContact_color_2, true)['transparency']],
                    'keepContact_background_color_1'         => ['color' => json_decode($request->keepContact_background_color_1, true)['transparency']],
                    'keepContact_background_color_2'         => ['color' => json_decode($request->keepContact_background_color_2, true)['transparency']],
                    'keepContact_selected_space_color'       => ['color' => json_decode($request->keepContact_selected_space_color, true)['transparency']],
                    'keepContact_unselected_space_color'     => ['color' => json_decode($request->keepContact_unselected_space_color, true)['transparency']],
                    'keepContact_closed_space_color'         => ['color' => json_decode($request->keepContact_closed_space_color, true)['transparency']],
                    'keepContact_text_space_color'           => ['color' => json_decode($request->keepContact_text_space_color, true)['transparency']],
                    'keepContact_names_color'                => ['color' => json_decode($request->keepContact_names_color, true)['transparency']],
                    'keepContact_thumbnail_color'            => ['color' => json_decode($request->keepContact_thumbnail_color, true)['transparency']],
                    'keepContact_countdown_background_color' => ['color' => json_decode($request->keepContact_countdown_background_color, true)['transparency']],
                    'keepContact_countdown_text_color'       => ['color' => json_decode($request->keepContact_countdown_text_color, true)['transparency']],
                ],
                'section_text'       => [
                    'reply_text'                => $request->reply_text,
                    'keepContact_section_line1' => $request->keepContact_section_line1,
                    'keepContact_section_line2' => $request->keepContact_section_line2,
                ],
            ],
        ];
    }
    
    
    /**
     * To prepare the bluejeans update or create params
     * @param Request $request
     * @return array
     */
    public function prepareBlueJeansParam($request) {
        $param = [
            'event_uses_bluejeans_event' => 0,
        ];
        if ($request && $request->has('event_uses_bluejeans_event') && $request->input('event_uses_bluejeans_event')) {
            $param = [
                'event_uses_bluejeans_event' => 1,
                'event_chat'                 => checkValSet($request->input('event_chat')),
                'attendee_search'            => checkValSet($request->input('attendee_search')),
                'q_a'                        => checkValSet($request->input('q_a')),
                'allow_anonymous_questions'  => checkValSet($request->input('allow_anonymous_questions')),
                'auto_approve_questions'     => checkValSet($request->input('auto_approve_questions')),
                'auto_recording'             => checkValSet($request->input('auto_recording')),
                'phone_dial_in'              => checkValSet($request->input('phone_dial_in')),
                'raise_hand'                 => checkValSet($request->input('raise_hand')),
                'display_attendee_count'     => checkValSet($request->input('display_attendee_count')),
                'allow_embedded_replay'      => checkValSet($request->input('allow_embedded_replay')),
            ];
        }
        return $param;
    }
    
    /**
     * @param Request $request
     * @param int $assoc
     * @return array|object
     */
    public function prepareSpaceCreateParam($request, $assoc = 1) {
        $lastSpaceOrderId = EventSpaceService::getInstance()->getLastSpaceOrderId($request->input('event_uuid'));
        $rank = KctService::getInstance()
            ->getLexoRank($lastSpaceOrderId);
        // so the rank will be assigned between last order id and z
        $param = [
            'space_name'                => $request->space_name,
            'space_short_name'          => $request->space_short_name,
            'space_mood'                => $request->space_mood,
            'max_capacity'              => $request->max_capacity,
            'space_image'               => $request->space_image,
            'space_image_from'          => $request->space_image_from, // to check if image is from system or stock
            'space_icon'                => $request->space_icon,
            'is_vip_space'              => $request->is_vip_space,
            'event_uuid'                => $request->event_uuid,
            'follow_main_opening_hours' => $request->does_not_follow_main_hour ? 0 : 1, // making reverse as does not to does
            'hosts'                     => $request->hosts,
            'opening_hours'             => [
                'after'  => $request->input('opening_hours_after', config('cocktail.default.opening_after')),
                'before' => $request->input('opening_hours_before', config('cocktail.default.opening_before')),
                'during' => $request->input('opening_hours_during', 0),
            ],
            'order_id'                  => $rank,
        ];
        if ($assoc) {
            return $param;
        }
        return (object)$param;
    }
    
    /**
     * This will prepare the space parameter which are possible to update
     * as during the event only some fields can be update
     *
     * @param Request $request
     * @return array
     * @throws CustomValidationException
     */
    public function prepareSpaceUpdateParam($request) {
        $space = EventSpace::with('event')->find($request->input('space_uuid'));
        if ($space && isset($space->event->event_uuid)) {
            if (ValidationService::getInstance()->isEventOrSpaceRunning($space->event)) {
                $param = [
                    'max_capacity' => $request->input('max_capacity'),
                ];
            } else {
                $param = [
                    'space_name'                => $request->space_name,
                    'space_short_name'          => $request->space_short_name,
                    'space_mood'                => $request->space_mood,
                    'max_capacity'              => $request->max_capacity,
                    'space_image'               => $request->space_image,
                    'space_icon'                => $request->space_icon,
                    'space_image_from'          => $request->space_image_from, // to check if image is from system or stock
                    'is_vip_space'              => $request->is_vip_space,
                    'follow_main_opening_hours' => $request->does_not_follow_main_hour ? 0 : 1,
                ];
            }
            if ($request->has("opening_hours_after")) {
                $isEventRunning = ValidationService::getInstance()->isEventRunning($space->event);
                $opening = $space->opening_hours;
                if ($isEventRunning) {
                    $opening['after'] = $request->input('opening_hours_after', config('cocktail.default.opening_after'));
                    $opening['before'] = $request->input('opening_hours_before', config('cocktail.default.opening_before'));
                } else {
                    $opening = [
                        'after'  => $request->input('opening_hours_after', config('cocktail.default.opening_after')),
                        'before' => $request->input('opening_hours_before', config('cocktail.default.opening_before')),
                        'during' => $request->input('opening_hours_during', 0),
                    ];
                }
                $param['opening_hours'] = $opening;
            }
            return $param;
        } else {
            throw new CustomValidationException('invalid_event', '', 'message');
        }
        
    }
}