<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\ColorRGBA;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Cocktail\Rules\EventAndSpaceNotStarted;
use Modules\Cocktail\Services\AuthorizationService;

class EventKeepContactRequest extends FormRequest {
    
    /**
     * To modify the data before validation , this will not affect to actual value in controller.
     *
     * @return array
     */
    protected function validationData() {
        $data =  parent::validationData();
        $data['keepContact_page_title'] = strip_tags($this->keepContact_page_title);
        $data['keepContact_section_line1'] = strip_tags($this->keepContact_section_line1);
        $data['keepContact_section_line2'] = strip_tags($this->keepContact_section_line2);
        return $data;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rgbaColorValidation = ['required', 'json', new ColorRGBA];
        $urlValidation = 'nullable|url';
        $validation = config('cocktail.validations.kct');
        $min = config('cocktail.validations.default_min');
        return [
            'event_uuid'                             => ['required', new EventAndSpaceNotStarted],
            // Page Customisation validation
            'keepContact_page_title'                 => "required|min:$min|max:{$validation['page_title']}",
            'keepContact_page_description'           => "required|min:$min|max:{$validation['page_description']}",
            'keepContact_page_logo'                  => 'nullable|image',
            'website_page_link'                      => $urlValidation,
            'twitter_page_link'                      => $urlValidation,
            'linkedIn_page_link'                     => $urlValidation,
            'facebook_page_link'                     => $urlValidation,
            'instagram_page_link'                    => $urlValidation,
            // Color Validation
            'hover_border_color'                     => $rgbaColorValidation,
            'main_background_color'                  => $rgbaColorValidation,
            'texts_color'                            => $rgbaColorValidation,
            'keepContact_color_1'                    => $rgbaColorValidation,
            'keepContact_color_2'                    => $rgbaColorValidation,
            'keepContact_background_color_1'         => $rgbaColorValidation,
            'keepContact_background_color_2'         => $rgbaColorValidation,
            'keepContact_selected_space_color'       => $rgbaColorValidation,
            'keepContact_unselected_space_color'     => $rgbaColorValidation,
            'keepContact_closed_space_color'         => $rgbaColorValidation,
            'keepContact_text_space_color'           => $rgbaColorValidation,
            'keepContact_names_color'                => $rgbaColorValidation,
            'keepContact_thumbnail_color'            => $rgbaColorValidation,
            'keepContact_countdown_background_color' => $rgbaColorValidation,
            'keepContact_countdown_text_color'       => $rgbaColorValidation,
            // KeepContact section texts validation
            'reply_text'                             => "required|string|min:$min|max:{$validation['reply_text']}",
            'keepContact_section_line1'              => "required|string|min:$min|max:{$validation['section_line1']}",
            'keepContact_section_line2'              => "required|string|min:$min|max:{$validation['section_line2']}",
        ];
    }
    
    /**
     * To authorize user to edit the configuration settings
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserEventAdmin($this->input('event_uuid'));
    }
    
    /**
     * Return the message in custom way if validation failed
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
