<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\EventTimeRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: CreateSpaceRequest",
 *  description="Validates the request body for creating a space ",
 *  type="object",
 *  required={
 *     "space_name",
 *     "event_uuid",
 *     "hosts",
 * },
 *  @OA\Property(property="space_name",type="string",description="Space Name",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood",example="Space Mood"),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="144"),
 *  @OA\Property(property="space_type",type="integer",description="Type of space",example="1", enum={"0", "1"}),
 *  @OA\Property(property="event_uuid",type="uuid",description="UUID of Event",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property( property="hosts",type="array",description="Host of the space",
 *                  @OA\Items(type="integer",example="1"),
 *  ),
 *  @OA\Property(property="is_mono",type="integer",description="If space type is mono",example="1", enum={"0", "1"}),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will validate the space related request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class CreateSpaceRequest
 * @package Modules\KctAdmin\Http\Requests
 */
class CreateSpaceRequest extends FormRequest {

    /**
     * @var array|string|null
     */
    private $authorizationMessage;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        $validation = config('kctadmin.modelConstants.spaces.validations');
        $default = config('kctadmin.modelConstants.spaces.defaults');

        return [
            // treating it as space line 1
            'space_name'       => "required|min:{$default['default_min']}|max:{$validation['space_line_1']}",
            // treating as space line 2
            'space_short_name' => "nullable|max:{$validation['space_line_2']}",
            'space_mood'       => "nullable",
            'max_capacity'     => "required|numeric|min:{$default['min_capacity']}|max:{$default['max_capacity']}",
            'event_uuid'       => ['required', new EventRule, new EventTimeRule(0, 0, 1)],
            'space_type'       => ['nullable', 'in:0,1'], // 0 for regular, 1 for vip
            'hosts'            => 'required|array|max:1',
            'hosts.*'          => "required|exists:tenant.users,id",
            'is_mono'          => "nullable",
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return void
     */
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorised",
        ], 403));
    }

}
