<?php

namespace Modules\Events\Exceptions;

use Exception;
use Throwable;

class CustomException extends Exception {
    protected $data;
    
    public function __construct($data = null, $message = "", $code = 0, Throwable $previous = null) {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }
    
    public function render() {
        return response()->json([
            'status' => false,
            'msg' => $this->getMessage(),
        ], 422);
    }
}
