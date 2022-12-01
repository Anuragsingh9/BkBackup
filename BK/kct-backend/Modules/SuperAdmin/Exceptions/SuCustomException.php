<?php

namespace Modules\SuperAdmin\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class SuCustomException extends Exception {
    public function __construct($keyName, $attribute = null, $fileName = null, $code = 422) {
        $attribute = $attribute ?: [];
        $fileName = $fileName ?: 'messages';
        $message = __("superadmin::$fileName.$keyName", $attribute);
        $this->code = $code;
        parent::__construct($message, $code);
    }

    public function render(): JsonResponse {
        return response()->json([
            'status' => false,
            'msg'    => $this->message,
            'trace'  => $this->getTrace(),
        ], $this->code);
    }
}
