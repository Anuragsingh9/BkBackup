<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: UserResource",
 *  description="This resource contains records of multiple user created",
 *     @OA\Property(property="field",type="string",description="Key of setting",example="main_color_1",
 *         enum={
 *                  "main_color_1","main_color_2","header_bg_color_1","header_separation_line_color",
 *                  "header_text_color","customized_join_button_bg","customized_join_button_text","sh_background",
 *                  "conv_background","badge_background","space_background","user_grid_background","event_tag_color",
 *                  "professional_tag_color","personal_tag_color","tags_text_color","apply_customisation",
 *                  "header_footer_customized","button_customized","texture_customized","texture_square_corners",
 *                  "texture_remove_frame","texture_remove_shadows","sh_customized","sh_hide_on_off",
 *                  "conv_customization","badge_customization","space_customization","unselected_spaces_square",
 *                  "selected_spaces_square","extends_color_user_guide","user_grid_customization",
 *                  "tags_customization","header_line_1","header_line_2","event_image","group_logo"
 *          },
 *     ),
 *     @OA\Property(property="value",type="string",
 *         description="Value will depend on the field type
 *         NOTE: when sending form data don't send the setting array from react directly to here because from react
 *     if you send object/array to form data key it will stringify it
 *             color type -> {r:3,g:3,b:1,a:0.3}
 *             text based send simple string
 *             for logo/image there will full url",example="r:3,g:3,b:1,a:0.3"),
 *     @OA\Property(property="is_default",type="integer",
 *          description="if value in present show 1",example="1", enum={"0", "1"}),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the group setting resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupSettingResource
 * @package Modules\KctAdmin\Transformers\V1
 */
class GroupSettingResource extends JsonResource {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $groupSetting = config('kctadmin.default.group_settings');

        $colorKeys = array_keys($groupSetting['colors']);
        $checkBoxKeys = array_keys($groupSetting['checkboxes']);
        $textKeys = array_keys($groupSetting['textBoxes']);
        $imageKeys = array_keys($groupSetting['images']);
        $arrayKeys = array_keys($groupSetting['arrays']);

        if (in_array($this->resource->setting_key, $colorKeys)) {
            // current setting is type of color related so handling according to that
            $value = isset($this->resource->setting_value[$this->resource->setting_key])
                // for color type of key it should be converted to rgba object
                ? $this->hexToRgba($this->resource->setting_value[$this->resource->setting_key])
                : null;
        } else if (in_array($this->resource->setting_key, $textKeys)) {
            // current setting key is type of  text so simply storing the setting value
            $value = $this->resource->setting_value[$this->resource->setting_key] ?? null;
        } else if (in_array($this->resource->setting_key, $imageKeys)) {
            // current setting is type of image so storing the resolved image path to return
            $value = isset($this->resource->setting_value[$this->resource->setting_key])
                ? $this->adminServices()->fileService->getFileUrl(
                    $this->resource->setting_value[$this->resource->setting_key]
                ) : null;
        } else if (in_array($this->resource->setting_key, $checkBoxKeys)) {
            // current setting key is type checkbox so storing the value 1 or 0 to return
            $value = (int)($this->resource->setting_value[$this->resource->setting_key] ?? 0);
        } else if (in_array($this->resource->setting_key, $arrayKeys)) {
            // current setting key is type of array so returning the array values
            $value = $this->filterArray();
        } else {
            $value = null;
        }
        $gridImage = null;
        // Check setting value for video explainer alternative image
        if ($this->resource->setting_key === 'video_explainer_alternative_image') {
            $gridImage = $this->adminServices()->superAdminService->getUserGridImage();
        }

        // checking if event image is default or not so front can show a cross icon
        $isEventDefaultImage = $this->resource->setting_key === 'event_image'
            && $this->resource->setting_value[$this->resource->setting_key] ==
            config('kctadmin.constants.event_default_image_path');

        // checking if grid image is default or not so front end can show a cross on default
        $isGridImageDefaultImage = $this->resource->setting_key === 'video_explainer_alternative_image'
            && $this->resource->setting_value['video_explainer_alternative_image'] == $gridImage;

        // checking if group logo is default or not so front end can show a cross on default
        $isGroupLogoDefaultImage = $this->resource->setting_key === 'group_logo'
            && $this->resource->setting_value[$this->resource->setting_key] ==
            config('kctadmin.constants.group_logo_default_image');

        $isDefault = $isEventDefaultImage || $isGridImageDefaultImage || $isGroupLogoDefaultImage;

        return [
            'field'      => $this->resource->setting_key,
            'value'      => $value,
            'is_default' => $this->when($isDefault, 1),
        ];
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method use for get the setting
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @return mixed
     */
    private function getSetting($key) {
        $setting = config('kctadmin.constants.setting_keys');
        return $this->resource->where('setting_key', $setting[$key])->first();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is use fo filtering the array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array|GroupSettingArrayResource
     */
    private function filterArray() {
        if ($this->isZoomKey($this->resource->setting_key)) {
            return new GroupSettingArrayResource($this->resource);
        }
        return $this->resource->setting_value ?? [];
    }


}
