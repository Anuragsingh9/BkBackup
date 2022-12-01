<?php

namespace Modules\Cocktail\SwaggerReference;

/**
 * @OA\Schema(
 *  title="APIResource: 500 Internal Server Error",
 *  description="Resource for showing 500 error code which represents Internal Server Error",
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
 *      example="Internal Server Error"
 *  ),
 * ),
 */
class Doc500Resource {
}