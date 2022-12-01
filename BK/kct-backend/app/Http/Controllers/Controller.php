<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;



/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="HumannConnect",
 *      description="HumannConnect API",
 *      @OA\Contact(
 *          email="gourav.verma@kct-technologies.com"
 *      ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *      url="http://first.kct.local/",
 *      description="Keep Contact Local Development Server"
 * )
 * @OA\Server(
 *      url="https://finaldemo.humannconnect.dev/",
 *      description="Keep Contact Local Development Server"
 * )
 * @OA\Server(
 *      url="https://stagingworkplace.humannconnect.dev/",
 *      description="Keep Contact Local Development Server"
 * )
 * @OA\Server(
 *      url="https://testingdev.seque.in/",
 *      description="Keep Contact Testing Dev Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="api_key",
 *     name="Authorization"
 * )
 *
 * @OA\Tag(name="Authenticate",description="User Authentication api"),
 * @OA\Tag(name="Event",description="Event management api"),
 * @OA\Tag(name="Group",description="Group management api"),
 * @OA\Tag(name="Organiser Tag",description="Organisation tags management api"),
 * @OA\Tag(name="Space",description="Event space management api"),
 * @OA\Tag(name="User",description="User management api"),
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
