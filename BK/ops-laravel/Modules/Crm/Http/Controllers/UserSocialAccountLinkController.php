<?php

namespace Modules\Crm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Crm\Entities\UserSocialAccountLink;
use Modules\Crm\Http\Requests\UserSocialLinkRequest;
use Modules\Crm\Http\Requests\UserSocialUpdateRequest;
use Validator;


class UserSocialAccountLinkController extends Controller {

    public function create(UserSocialLinkRequest $request) {
        $columns = [
            'user_id'    => (($request->type == 'user') ? $request->id : NULL),
            'contact_id' => (($request->type == 'contact') ? $request->id : NULL),
            'channel'    => $request->channel,
            'url'        => $request->url,
        ];
        try {
            // Check Duplicate entry
            $isDuplicateEntry = UserSocialAccountLink::where($columns)->count();
            if ($isDuplicateEntry) {
                return response()->json(['status' => FALSE, 'msg' => 'This Link Already Exists'], 422);
            }
            /*
             * linkedin=  1
             * facebook = multiple
             * twitter = multiple
             * instagram = multiple
             * pinterest = multiple
             */
            if (strtolower($request->channel) == "linkedin") {
                $userSocialAccountLinkCount = UserSocialAccountLink::where([
                    'user_id'    => (($request->type == 'user') ? $request->id : NULL),
                    'contact_id' => (($request->type == 'contact') ? $request->id : NULL),
                    'channel'    => $request->channel,
                ])->count();
                if ($userSocialAccountLinkCount) {
                    return response()->json([
                        'status' => FALSE,
                        'msg'    => 'There can be only single link with ' . $request->channel
                    ], 422);
                }
            }
            $columns['is_main'] = ((strtolower($request->channel)) == 'linkedin' ? 1 : $request->isMain);
            $userSocialAccountLink = UserSocialAccountLink::create($columns);
            if ($userSocialAccountLink) {
                if (strtolower($request->channel) != 'linkedin' && $request->isMain) {
                    UserSocialAccountLink::where([
                        'user_id' => $request->userId,
                        'channel' => $request->channel,
                    ])->where('id', '!=', $userSocialAccountLink->id)->update(['is_main' => 0]);
                }
                return response()->json(['status' => TRUE, 'data' => $userSocialAccountLink], 200);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Record Not Created'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => TRUE, 'data' => ''], 500);
        }

    }

    public function get_user_social_links(Request $request, $type, $id) {
        if (!$id) {
            return response()->json([
                'status' => FALSE,
                'msg'    => 'No id provided',
            ], 422);
        }

        $type = $type == 'user' ? 'user_id' : 'contact_id';

        $userSocialAccountLink = UserSocialAccountLink::where($type, $id)->get();
        if(!$userSocialAccountLink->count()) {
            return response()->json(['status' => true, 'data' => []], 200);
        }
        $data = [];
        foreach ($userSocialAccountLink as $link) {
            $data[$link->channel][] = $link;
        }
        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function remove_social_link(Request $request, $id) {
        // USAL = User Social Account Link
        $usal = UserSocialAccountLink::where('id', $id);
        if (!$usal->count()) {
            return response()->json(['status' => FALSE, 'msg' => 'Record Not Found'], 422);
        }
        if ($usal->delete() == 0) {
            return response()->json(['status' => FALSE, 'data' => []], 500);
        }
        return response()->json(['satus' => TRUE, 'data' => 'Successfully Removed Link(s)'], 200);
    }

    public function update(UserSocialUpdateRequest $request) {
        try {
            $link = UserSocialAccountLink::find($request->user_social_id);
            $flag = $link->update([
                'url' => $request->url,
                'is_main' => ($link->channel == 'linkedin' ? 1 : $request->is_main),
            ]);
            if($flag) {
                return response()->json(['satus' => TRUE, 'msg' => 'Successfully Updated'], 200);
            }
            return response()->json(['status' => TRUE, 'data' => []], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => TRUE, 'data' => []], 500);
        }

    }

    public function test() {
        return dd();
    }

}
