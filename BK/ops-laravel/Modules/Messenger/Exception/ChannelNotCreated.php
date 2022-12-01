<?php

namespace Modules\Messenger\Exception;

use Exception;

class ChannelNotCreated extends Exception
{
    public function render() {
        return response()->json(['status' =>false, 'msg' => 'we are in custom exception'], 500);
    }
}
