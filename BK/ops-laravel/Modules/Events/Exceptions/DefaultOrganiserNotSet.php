<?php

namespace Modules\Events\Exceptions;

use Exception;

class DefaultOrganiserNotSet extends Exception {
    public function render() {
        return response()->json(['status'=> false, 'msg' => __('message.organiserNotSet')], 422);
    }
}
