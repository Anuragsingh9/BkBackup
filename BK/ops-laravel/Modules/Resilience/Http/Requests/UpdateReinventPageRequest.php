<?php

namespace Modules\Resilience\Http\Requests;

use Modules\Resilience\Rules\ReinventJson;
use Modules\Resilience\Rules\StripStringLength;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateReinventPageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $regex = '/^(?:https?:\/\/)?(?:www\.)?([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        // $regex = '/^(https?:\/\/)?((([a-z\\\\d]([a-z\\\\d-]*[a-z\\\\d])*)\\\\.)+[a-z]{2,}|((\\\\d{1,3}\\\\.){3}\\\\d{1,3}))(\\\\:\\\\d+)?(\\\\[-a-z\\\\d%_.~+]*)*(\\\\?[;&a-z\\\\d%_.~+=-]*)?(\\\\#[-a-z\\\\d_]*)*\/?$/m';
        $twitter = '/^(?:https?:\/\/)?(?:www\.)?twitter\.com/';//\/([a-zA-Z0-9_]+)
        $linkedin = '/^(?:https?:\/\/)?(?:www\.)?linkedin\.com/';//\/in\/([a-zA-Z0-9_]+)
        $facebook = '/^(?:https?:\/\/)?(?:www\.)facebook\.com/';//\/([a-zA-Z0-9_]+)
        $instagram = '/^(?:https?:\/\/)?(?:www\.)instagram\.com/';//\/([a-zA-Z0-9_]+)
        return [
            'title'                             => ['sometimes', 'string', 'min:3', new StripStringLength(config('resilience.limit.title'))],
            'description'                       => 'sometimes|string|min:3|max:' . config('resilience.limit.description'),
            'logo'                              => 'sometimes|image|mimes:jpeg,png,jpg',
            'bottomImage'                       => 'sometimes|image|mimes:jpeg,png,jpg',
            'bottomText'                        => 'sometimes|string|max:255',
            'website'                           => 'sometimes|nullable|regex:'.$regex,
            'twitter'                           => 'sometimes|nullable|regex:'.$twitter,
            'linkedin'                          => 'sometimes|nullable|regex:'.$linkedin,
            'facebook'                          => 'sometimes|nullable|regex:'.$facebook,
            'instagram'                         => 'sometimes|nullable|regex:'.$instagram,
            'replayText'                        => 'sometimes|string|max:100|min:3',
            'footerTextLineOne'                 => ['sometimes', 'string', new StripStringLength(config('resilience.limit.reinvent_footer')), 'min:3'],
            'footerTextLineTwo'                 => ['sometimes', 'string', new StripStringLength(config('resilience.limit.reinvent_footer')), 'min:3'],
            'sectionLineOne'                    => ['sometimes', 'string', new StripStringLength(config('resilience.limit.title')), 'min:3'],
            'sectionLineTwo'                    => ['sometimes', 'string', new StripStringLength(config('resilience.limit.title')), 'min:3'],
            'lightTextColor'                    => ['sometimes', 'json', new ReinventJson],
            'mediumTextColor'                   => ['sometimes', 'json', new ReinventJson],
            'darkTextColor'                     => ['sometimes', 'json', new ReinventJson],
            'highlightTextColor'                => ['sometimes', 'json', new ReinventJson],
            'lightBackGroundColor'              => ['sometimes', 'json', new ReinventJson],
            'darkBackGroundColor'               => ['sometimes', 'json', new ReinventJson],
            'highlightBackGroundColor'          => ['sometimes', 'json', new ReinventJson],
            'bottomTextColor'                   => ['sometimes', 'json', new ReinventJson],
            'shapeBackColor'                    => ['sometimes', 'json', new ReinventJson],
            'shapeActiveBackColor'              => ['sometimes', 'json', new ReinventJson],
            'stickerGradiantLeftColor'          => ['sometimes', 'json', new ReinventJson],
            'stickerActiveGradiantLeftColor'    => ['sometimes', 'json', new ReinventJson],
            'stickerActiveGradiantRightColor'   => ['sometimes', 'json', new ReinventJson],
            'stickerGradiantRightColor'         => ['sometimes', 'json', new ReinventJson],
            'stickerTextColor'                  => ['sometimes', 'json', new ReinventJson],
            'stickerActiveTextColor'            => ['sometimes', 'json', new ReinventJson],
            'shapeTextColor'                    => ['sometimes', 'json', new ReinventJson],
            'shapeActiveTextColor'              => ['sometimes', 'json', new ReinventJson],
            'circleGradiantLeftColor'           => ['sometimes', 'json', new ReinventJson],
            'circleGradiantRightColor'          => ['sometimes', 'json', new ReinventJson],
            'circleActiveGradiantLeftColor'     => ['sometimes', 'json', new ReinventJson],
            'circleActiveGradiantRightColor'    => ['sometimes', 'json', new ReinventJson],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
