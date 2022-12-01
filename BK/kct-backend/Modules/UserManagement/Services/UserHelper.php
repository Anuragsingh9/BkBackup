<?php


namespace Modules\UserManagement\Services;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UserHelper {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the internal server error
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Exception $e
     * @return JsonResponse
     */
    public static function internalServerException(Exception $e): JsonResponse {
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'development') {
            return response()->json([
                'status' => false,
                'msg'    => 'Internal server error' . $e->getMessage(),
                'error'  => $e->getTrace(),
            ], 500);
        } else {
            return response()->json([
                'status' => false,
                'msg'    => 'Internal server error' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the 422 response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $message
     * @return JsonResponse
     */
    public static function send422($message): JsonResponse {
        return response()->json(['status' => false, 'msg' => $message], 422);
    }

    /**
     * @return string
     */
    public static function randomString():string{
        return  Str::random(60);
    }
}
