<?php

namespace Modules\KctAdmin\SwaggerReference;

/**
 * @OA\Schema(
 *  title="APIResource: 422 Validation Fail",
 *  description="Resource for showing 422 error code which represents Validation errors",
 *  @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="false"),
 *  @OA\Property(property="msg",type="string",description="Result message of validation failiure",example="Field is required"),
 *  @OA\Property(property="errors",type="array",description="Array of all error messages",
 *     @OA\Items(
 *          @OA\Property(property="row",type="integer",description="Row Number of error",example="1"),
 *          @OA\Property(property="attribute",type="integer",description="Column Number of error",example="1"),
 *          @OA\Property(property="errors",type="array",description="Error Messages for specific property",@OA\Items(type="string", example="Error Message")),
 *          @OA\Property(property="values",type="array",description="Values of row",@OA\Items(type="string", example="FirstName 1")),
 *
 *      )
 *  ),
 * ),
 */
class Doc422MaatwebResource {
}
