<?php

namespace Modules\KctAdmin\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class CustomValidationException extends Exception {

    public function __construct($key = null, $attribute = null, $moduleResourceFileName = 'validation', $code = 0, Throwable $previous = null) {
        if ($moduleResourceFileName != 'validation') { // if no other translation file given that means we are using module resource
            // so use the module kctadmin to get the resource from the module
            // if attribute given we need to translate that also and add it to replace param
            $message = $attribute
                ? __("kctadmin::$moduleResourceFileName.$key", ['attribute' => __("kctadmin::words.$attribute")])
                : __("kctadmin::$moduleResourceFileName.$key");
        } else {
            $message = $attribute
                ? __("$moduleResourceFileName.$key", ['attribute' => __("kctadmin::words.$attribute")])
                : __("$moduleResourceFileName.$key");
        }
        parent::__construct($message, $code, $previous);
    }

    public function render(): JsonResponse {
        return response()->json(['status' => false, 'msg' => $this->getMessage()], 422);
    }
}