<?php

namespace Modules\KctUser\Exceptions;

use Exception;
use Throwable;

class CustomAuthorizationException extends Exception {

    public function __construct($key = null, $attribute = null, $moduleResourceFileName = 'message', $code = 0, Throwable $previous = null) {
        $message = $attribute
            ? __("kctuser::$moduleResourceFileName.$key", ['attribute' => __("kctuser::words.$attribute")])
            : __("kctuser::$moduleResourceFileName.$key");
        parent::__construct($message, $code, $previous);
    }

    public function render() {
        return response()->json([
            'status' => false,
            'msg'    => $this->getMessage(),
        ], 403);
    }
}
