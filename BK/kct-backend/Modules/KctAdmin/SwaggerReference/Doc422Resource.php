<?php

namespace Modules\KctAdmin\SwaggerReference;

/**
 * @OA\Schema(
 *  title="APIResource: 422 Validation Fail",
 *  description="Resource for showing 422 error code which represents Validation errors",
 *  @OA\Property(
 *      property="status",
 *      type="boolean",
 *      description="To indicate server processed request properly",
 *      example="false"
 *  ),
 *  @OA\Property(
 *      property="msg",
 *      type="string",
 *      description="String which contains all the error messages with '.' separated",
 *      example="Field is required"
 *  ),
 * ),
 */
class Doc422Resource {
}
