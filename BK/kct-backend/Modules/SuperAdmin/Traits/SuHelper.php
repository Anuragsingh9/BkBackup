<?php


namespace Modules\SuperAdmin\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the reusable helper functions globally in Super Admin Module
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Trait SuHelper
 * @package Modules\SuperAdmin\Services
 */
trait SuHelper {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the Internal Server Error Exception and return the Json Response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Exception $exception
     * @return JsonResponse
     */
    public function handleIse(Exception $exception): JsonResponse {
        return response()->json([
            'status' => false,
            'msg'    => $exception->getMessage(),
            'trace'  => $exception->getTrace(),
        ], 500);
    }

    public function randomString(): string {
        return Str::random(60);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the subdomain from the url
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $url
     * @return string|null
     */
    public function getSubdomain($url): ?string {
        $url = parse_url($url);
        $url = $url['host'] ?? $url['path'] ?? $url;
        $url = explode(".", $url);
        return count($url) >= 3 ? $url[0] : null;
    }
}
