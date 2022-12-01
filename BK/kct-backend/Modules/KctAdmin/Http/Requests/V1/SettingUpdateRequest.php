<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\ColorRGBARule;
use Modules\KctAdmin\Rules\GroupSettingRule;
use Modules\KctAdmin\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="RequestValidation: Virtual Event Request Validation",
 *  description="Validates the request body for creating a virtual event ",
 *  type="object",
 *  required={"group_key"},
 *  @OA\Property(property="group_key",type="string",description="Group key for the tag",example="default"),
 *  @OA\Property(property="settings",type="array",description="Set of fields to update",
 *      @OA\Items(
 *          @OA\Property(property="field",type="string",description="Key of setting",example="main_color_1",
 *              enum={
 *                      "main_color_1","main_color_2","header_bg_color_1","header_separation_line_color",
 *                      "header_text_color","customized_join_button_bg","customized_join_button_text",
 *                      "sh_background","conv_background","badge_background","space_background","user_grid_background",
 *                      "event_tag_color","professional_tag_color","personal_tag_color","tags_text_color",
 *                      "apply_customisation","header_footer_customized","button_customized","texture_customized",
 *                      "texture_square_corners","texture_remove_frame","texture_remove_shadows","sh_customized",
 *                      "sh_hide_on_off","conv_customization","badge_customization","space_customization",
 *                      "unselected_spaces_square","selected_spaces_square","extends_color_user_guide",
 *                      "user_grid_customization","tags_customization","header_line_1","header_line_2","event_image",
 *                      "group_logo","zoom_default_webinar_settings","zoom_webinar_settings","zoom_meeting_settings"
 *              },
 *          ),
 *          @OA\Property(property="value",type="string",
 *              description="Value will depend on the field type
 *              NOTE: when sending form data don't send the setting array from react directly to here because from react
 *                    if you send object/array to form data key it will stringify it
 *                          color type send json encoded string -> {r:3,g:3,b:1,a:0.3}
 *                          text based send simple string
 *                          for logo/image update send in form data with image",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
 *     ),
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the design setting update request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SettingUpdateRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class SettingUpdateRequest extends FormRequest {

    use KctHelper;

    private array $colorKeys;
    private array $checkBoxKeys;
    private array $textKeys;
    private array $imageKeys;
    private array $arrayKeys;

    private array $allKeys;
    private string $allKeysImplode;

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null) {
        parent::__construct(
            $query,
            $request,
            $attributes,
            $cookies,
            $files,
            $server,
            $content
        );
        $groupSetting = config('kctadmin.default.group_settings');

        $this->colorKeys = $groupSetting['colors'];
        $this->checkBoxKeys = $groupSetting['checkboxes'];
        $this->textKeys = $groupSetting['textBoxes'];
        $this->imageKeys = $groupSetting['images'];
        $this->arrayKeys = $groupSetting['arrays'];

        $this->allKeys = $this->getGraphicKeys();
        $this->allKeysImplode = implode(',', $this->allKeys);

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'settings'         => "required|array",
            'settings.*'       => "required|array|max:2",
            'settings.*.field' => "required|in:$this->allKeysImplode",
            'group_key'         => "required|exists:tenant.groups,group_key",
        ];
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for validate the instance
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Validator
     */
    protected function getValidatorInstance(): Validator {
        $validator = parent::getValidatorInstance();
        if (is_array($settings = $this->input('settings'))) {
            foreach ($settings as $i => $setting) {
                $validator = $this->putValidation($validator, $i, $setting);
            }
        }
        return $validator;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method validate the put specific index of the setting key.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     * @param $i
     * @param $value
     * @return Validator
     */
    private function putValidation(Validator $validator, $i, $value): Validator {
        $attribute = "settings.$i.value";
        $rules = [
            [
                'rule'  => ['required', new ColorRGBARule],
                'check' => $this->colorKeys,
            ], [
                'rule'  => 'nullable|string',
                'check' => $this->textKeys,
            ], [
                'rule'  => 'required|in:0,1',
                'check' => $this->checkBoxKeys,
            ], [
                'rule'  => 'nullable|image',
                'check' => $this->imageKeys,
            ], [
                'rule'  => [
                    'required',
                    'array',
                    new GroupSettingRule($this->input("settings.$i.field"), 'array')
                ],
                'check' => $this->arrayKeys
            ],
        ];

        foreach ($rules as $rule) {
            $iconRule = $this->validateIcons($i); //validating icons to upload
            $rule['rule'] = $iconRule ? $iconRule : $rule['rule'];
            $validator->sometimes($rule['atr'] ?? $attribute, $rule['rule'],
                function () use ($i, $rule) {
                    return in_array($this->input("settings.$i.field"), array_keys($rule['check']));
                }
            );
        }

        return $validator;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Validate the design setting icons
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $i
     * @return string|null
     */
    public function validateIcons($i): ?string {
        if (in_array($this->input("settings.$i.field"), [
            'vip_icon',
            'business_team_icon',
            'expert_icon',
            'vip_altImage',
            'expert_altImage',
            'vip_altImage'
        ])) {
            return 'nullable|image|max:1024';
        } else {
            return null;
        }
    }
}
