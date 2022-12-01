<?php


namespace Modules\KctUser\Repositories\factory;

use Modules\KctUser\Repositories\IDummyConvRepository;
use Modules\UserManagement\Entities\DummyUser;

class DummyConvRepository implements IDummyConvRepository {
    public function getDummyUserConversation($convUuid) {
        DummyUser::where('conversation_uuid',$convUuid)->update(['current_conv_uuid' => null]);
    }
}
