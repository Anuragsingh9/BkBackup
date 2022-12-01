<?php

namespace Modules\KctUser\Exceptions;

use Exception;

/**
 * This is exception when user try to leave the event when already leave or without join
 */
class EventLeaveAgainException extends Exception {

}
