<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\SpaceExistsRule;
use Modules\KctAdmin\Rules\SpaceTypeRule;
use Modules\KctAdmin\Rules\SpaceFutureRule;
use Modules\KctAdmin\Rules\SpaceTypeUpdateRule;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;
use Modules\KctAdmin\Traits\ServicesAndRepo;


/**
 * @OA\Schema(
 *  title="RequestValidation: UpdateSpaceRequest",
 *  description="Validates the request body for updating a space ",
 *  type="object",
 *  @OA\Property(property="space_uuid",type="uuid",description="UUID of Space",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(property="space_name",type="string",description="Space Name",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space Short Name"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood",example="Space Mood"),
 *  @OA\Property(property="space_type",type="integer",description="Type of spaces",example="1"),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="111"),
 *  @OA\Property(property="hosts",type="array",description="Hosts ID",@OA\Items(type="integer", example="1"),
 *  @OA\Property(property="header_line_1",type="string",
 *     description="To update the header line of event additionally",example="Header Line"
 *  ),
 *  @OA\Property(property="header_line_2",type="string",
 *     description="To update the header line of event additionally",example="Header Line"
 *  ),
 *  @OA\Property(property="is_self_header",type="integer",
 *     description="To indicate if event has own header",example="1", enum={"0", "1"}
 *  ),
 *  @OA\Property(property="is_mono",type="integer",description="If space type is mono",example="1", enum={"0", "1"}),
 *  @OA\Property(property="space_type",type="integer",description="Type of space",example="1", enum={"0", "1"}),
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will use for validate the update space request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateSpaceRequest
 * @package Modules\KctAdmin\Http\Requests
 */
class UpdateSpaceRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $eventValidation = config("kctadmin.modelConstants.events.validations");
        $spaceValidation = config('kctadmin.modelConstants.spaces.defaults');
        return [
            'space_uuid'       => [
                'required',
                'exists:tenant.event_spaces,space_uuid',
                new SpaceFutureRule($this->space_uuid, $this->space_type)
            ],
            // treating it as space line 1
            'space_name'       => "nullable",
            // treating as space line 2
            'space_short_name' => "nullable",
            'space_mood'       => "nullable",
            'max_capacity'     => "required|numeric|min:{$spaceValidation['min_capacity']}| max:{$spaceValidation['max_capacity']}",
            'hosts'            => [
                "required",
                "exists:tenant.users,id",
            ],
            'header_line_1'    => "nullable|max:{$eventValidation['header1_max']}",
            'header_line_2'    => "nullable|max:{$eventValidation['header2_max']}",
            'is_self_header'   => 'nullable|in:0,1',
            'is_mono'          => 'nullable',
            'space_type'       => ['nullable', 'in:0,1'], // 0 for regular, 1 for vip
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

}
