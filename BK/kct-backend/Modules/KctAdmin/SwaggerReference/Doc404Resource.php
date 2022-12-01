<?php

namespace Modules\KctAdmin\SwaggerReference;

/**
 * @OA\Schema(
 *  title="APIResource: 404 Resource not found",
 *  description="To show that the selected resource is not found",
 *  @OA\Property(
 *      property="status",
 *      type="boolean",
 *      description="To indicate server processed request properly",
 *      example="false"
 *  ),
 *  @OA\Property(
 *      property="msg",
 *      type="string",
 *      description="To send resource is not found",
 *      example="Unauthorized !."
 *  ),
 * ),
 */
class Doc404Resource {
}
