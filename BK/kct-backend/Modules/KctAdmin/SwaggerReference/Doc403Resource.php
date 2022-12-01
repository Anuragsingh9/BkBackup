<?php

namespace Modules\KctAdmin\SwaggerReference;

/**
 * @OA\Schema(
 *  title="APIResource: 403 Unauthentication",
 *  description="Resource for showing user is unauthorized to access this api",
 *  @OA\Property(
 *      property="status",
 *      type="boolean",
 *      description="To indicate server processed request properly",
 *      example="false"
 *  ),
 *  @OA\Property(
 *      property="msg",
 *      type="string",
 *      description="To Send something has gone wrong on server side.",
 *      example="Unauthorized !."
 *  ),
 * ),
 */
class Doc403Resource {
}
