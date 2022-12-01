<?php

namespace Modules\KctAdmin\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class DefaultGroupNotFoundException extends Exception {


    public function render(): JsonResponse {
        return response()->json([
            'status'  => false,
            'message' => __('kctadmin::messages.default_group_not_found'),
            'errors'  => [
                'group' => [
                    __('kctadmin::messages.default_group_not_found')
                ],
            ],
        ], 422);
    }
}
