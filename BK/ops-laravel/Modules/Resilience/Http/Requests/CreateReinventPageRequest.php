<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReinventPageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'title'                     => 'required|string|max:255',
//            'description'               => 'required|string|max:3000',
//            'logo'                      => 'required|image|mimes:jpeg,png,jpg,gif,svg',
//            'website'                   => 'sometimes|string|max:255',
//            'twitter'                   => 'sometimes|string|max:255',
//            'linkedin'                  => 'sometimes|string|max:255',
//            'facebook'                  => 'sometimes|string|max:255',
//            'instagram'                 => 'sometimes|string|max:255',
//            'replayText'                => 'required|string|max:255',
//            'sectionLineOne'            => 'required|string|max:255',
//            'sectionLineTwo'            => 'required|string|max:255',
//            'mainBackgroundColor'       => 'required|string',
//            'textsColor'                => 'required|string',
//            'color1'                    => 'required|string',
//            'color2'                    => 'required|string',
//            'backGroundColor1'          => 'required|string',
//            'backGroundColor2'          => 'required|string',
//            'selectedSpaceColor'        => 'required|string',
//            'unSelectedSpaceColor'      => 'required|string',
//            'closedSpaceColor'          => 'required|string',
//            'textSpaceColor'            => 'required|string',
//            'namesColor'                => 'required|string',
//            'thumbnailsColor'           => 'required|string',
//            'countdownBackgroundColor'  => 'required|string',
//            'countdownTextColor'        => 'required|string',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        logger('request----------------------------------', [$this->name]);
        return true;
    }
}
