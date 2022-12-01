<?php

namespace App\Exceptions;

use Exception;

class CustomValidationException extends Exception {
    
    public function render() {
        return response()->json(['status' => FALSE, 'msg' => $this->getMessage()], 422);
    }
}
