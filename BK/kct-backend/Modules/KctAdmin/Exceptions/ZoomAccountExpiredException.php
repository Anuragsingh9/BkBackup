<?php

namespace Modules\KctAdmin\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ZoomAccountExpiredException extends Exception {
    public function render(): JsonResponse {
        return response()->json([
            'status'  => false,
            'message' => __('kctadmin::messages.zoom_account_expired'),
            'errors'  => [
                'event_broadcasting' => [
                    __('kctadmin::messages.zoom_account_expired')
                ],
            ],
        ], 422);
    }
}
