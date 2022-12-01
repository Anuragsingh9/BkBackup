<?php

namespace Modules\Events\Exceptions;

use Exception;
use Modules\Cocktail\Services\KctService;
use Throwable;

class CustomValidationException extends Exception {
    
    public function __construct($key = null, $attribute = null, $moduleResourceFileName = 'validation', $code = 0, Throwable $previous = null) {
        $moduleName = "events";
        if ($moduleResourceFileName != 'validation') { // if no other translation file given that means we are using module resource
            // so use the module cocktail to get the resource from the module
            // if attribute given we need to translate that also and add it to replace param
            $message = $attribute
                ? __("$moduleName::$moduleResourceFileName.$key", ['attribute' => __("$moduleName::words.$attribute")])
                : __("$moduleName::$moduleResourceFileName.$key");
        } else {
            // if module resource not specified use root validation file for trans
            $message = $attribute
                ? __("$moduleResourceFileName.$key", ['attribute' => __("$moduleName::words.$attribute")])
                : __("$moduleResourceFileName.$key");
        }
        parent::__construct($message, $code, $previous);
    }
    
    public function render() {
        return response()->json(['status' => false, 'msg' => $this->getMessage()], 422);
    }
}
