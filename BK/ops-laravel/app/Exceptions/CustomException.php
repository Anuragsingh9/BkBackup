<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CustomException extends Exception {
    
    private $errCode;
    
    public function __construct($message = "", $code = 422, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->errCode = $code;
    }
    
    public function render() {
        return response()->json([
            'status' => FALSE,
            'msg'    => $this->getMessage(),
            'trace'  => $this->getTrace(),
        ], $this->errCode);
    }
}
