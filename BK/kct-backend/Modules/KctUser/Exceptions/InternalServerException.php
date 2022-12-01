<?php

namespace Modules\KctUser\Exceptions;

use Exception;
use Modules\KctUser\Services\KctService;
use Throwable;

class InternalServerException extends Exception {

    public function __construct($key = 'something_went_wrong', $code = 500, Throwable $previous = null) {
        $message = __("kctuser::exception_message.$key");
        parent::__construct($message, $code, $previous);
    }

    public function render() {
        return response()->json(['status' => false, 'msg' => $this->getMessage()], $this->getCode());
    }
}
