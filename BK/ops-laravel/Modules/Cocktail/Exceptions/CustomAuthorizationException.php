<?php

namespace Modules\Cocktail\Exceptions;

use Exception;
use Throwable;

class CustomAuthorizationException extends Exception {
    
    public function __construct($key = null, $attribute = null, $moduleResourceFileName = 'message', $code = 0, Throwable $previous = null) {
        $message = $attribute
            ? __("cocktail::$moduleResourceFileName.$key", ['attribute' => __("cocktail::words.$attribute")])
            : __("cocktail::$moduleResourceFileName.$key");
        parent::__construct($message, $code, $previous);
    }
    
    public function render() {
        return response()->json([
            'status' => false,
            'msg'    => $this->getMessage(),
        ], 403);
    }
}
