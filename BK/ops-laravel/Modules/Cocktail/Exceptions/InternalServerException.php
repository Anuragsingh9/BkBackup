<?php

namespace Modules\Cocktail\Exceptions;

use Exception;
use Modules\Cocktail\Services\KctService;
use Throwable;

class InternalServerException extends Exception {
    
    public function __construct($key = 'something_went_wrong', $code = 500, Throwable $previous = null) {
        $message = __("cocktail::exception_message.$key");
        parent::__construct($message, $code, $previous);
    }
    
    public function render() {
        return response()->json(['status' => false, 'msg' => $this->getMessage()], $this->getCode());
    }
}
