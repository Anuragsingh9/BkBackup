<?php


namespace Modules\UserManagement\Traits;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Modules\UserManagement\Repositories\BaseRepository;
use Modules\UserManagement\Services\BaseService;

trait UmHelper {
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

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will take input string and will check if the request attribute string is coming inside
     * array or directly
     *
     * e.g. users.0.fname -> there is attribute is fname, but it is coming in array so only attribute should be return
     * e.g. fname -> there is directly attribute coming, so no index value prepared
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function setAttribute($attribute): array {
        $exp = explode('.', $attribute);
        $atr = Arr::last($exp);
        if(count($exp) == 3) { // inside single array
            return [
                'key' => $exp[0] ?? null,
                'index' => $exp[1] ?? null,
                'attribute' => $atr,
            ];
        } else {
            return [
                'key' => null,
                'index' => null,
                'attribute' => $atr,
            ];
        }
    }

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
}
