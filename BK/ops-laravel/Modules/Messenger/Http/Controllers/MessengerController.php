<?php

namespace Modules\Messenger\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Message;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Messenger\Http\Requests\MessageAttachmentRequest;
use Modules\Messenger\Http\Requests\MessageDeleteRequest;
use Modules\Messenger\Http\Requests\MessageMultipleAttachmentRequest;
use Modules\Messenger\Http\Requests\MessageReactionRequest;
use Modules\Messenger\Http\Requests\MessageReplyDeleteRequest;
use Modules\Messenger\Http\Requests\MessageReplyRequest;
use Modules\Messenger\Http\Requests\MessageReplyUpdateRequest;
use Modules\Messenger\Http\Requests\MessageStoreRequest;
use Modules\Messenger\Http\Requests\MessageUpdateRequest;
use Modules\Messenger\Service\MessageService;
use Modules\Messenger\Transformers\MessageReplyResource;
use Modules\Messenger\Transformers\MessageResource;
use Validator;

class MessengerController extends Controller {
    // new created method
    /**
     * @param MessageStoreRequest $request
     * @return JsonResponse|MessageResource
     */
    public function store(MessageStoreRequest $request) {
        try {
            DB::connection('tenant')
                ->beginTransaction(); // will only apply models with TenantModel only // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $param = [
                'message_text' => $request->text,
                'channel_uuid' => $request->channel_uuid,
                'sender_id'    => Auth::user()->id,
                'url'          => json_decode($request->url, 1), // array of urls for message attachments
            ];
            $message = $service->sendMessage($param);   // store and get message model
            DB::connection('tenant')->commit();
            return (new MessageResource($message))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param MessageUpdateRequest $request
     * @return JsonResponse|MessageResource
     */
    public function update(MessageUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $param = [
                'message_text' => $request->text,
                'url'          => $request->url, // array of urls for message attachments
            ];
            $message = $service->updateMessage($request->message_id, $param); // update the message and send message model
            DB::connection('tenant')->commit();
            return (new MessageResource($message))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param MessageReplyRequest $request
     * @return JsonResponse|MessageReplyResource
     */
    public function reply(MessageReplyRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $param = [
                'message_id' => $request->message_id,
                'reply_text' => $request->text,
                'replied_by' => Auth::user()->id,
                'url'        => json_decode($request->url, 1), // array of urls for message attachments
            ];
            $message = $service->replyMessage($param); // store reply of message and send message reply model
            DB::connection('tenant')->commit();
            return (new MessageReplyResource($message))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param MessageReplyUpdateRequest $request
     * @return JsonResponse|MessageReplyResource
     */
    public function replyUpdate(MessageReplyUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $param = [
                'reply_text' => $request->text,
            ];
            $message = $service->replyUpdate($param, $request->message_reply_id); // update the reply and send reply model
            DB::connection('tenant')->commit();
            return (new MessageReplyResource($message))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param MessageReplyDeleteRequest $request
     * @return JsonResponse
     */
    public function replyDelete(MessageReplyDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $service->replyDelete($request->message_reply_id);  // delete single reply
//            $message = $service->getReplies($request->message_id);
            DB::connection('tenant')->commit();
            return MessageService::send200('Reply Deleted');    // message
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param MessageDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(MessageDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $service->delete($request->message_id); // delete the message and because of message boot method all the dependencies will also delete like reply media reaction
            DB::connection('tenant')->commit();
            return MessageService::send200('Deleted');
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param MessageReactionRequest $request
     * @return JsonResponse|mixed
     * Toggle , calling this api again will toggle
     */
    public function toggleReaction(MessageReactionRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
            $param = [
                'message_id'    => $request->message_id,
                'reaction_type' => $request->reaction_type, // 1 Star, 2 Like
                'reacted_by'    => Auth::user()->id,
            ];
            $message = $service->toggleReaction($param, $request->message_id); // toggle the message reaction
            DB::connection('tenant')->commit();
            return (new MessageResource($message))->additional(['status' => TRUE]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    /**
     * @param $messageId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getReplies($messageId) {
        $service = MessageService::getInstance();  // now getting instance inside method so methods which not required service object will save heap memory
        $replies = $service->getReplies($messageId); // get the replies of message
        return $replies ? MessageReplyResource::collection($replies) : MessageService::send200();
    }
    
    /**
     * @param MessageAttachmentRequest $request
     * @return JsonResponse|mixed
     */
    public function uploadFile(MessageAttachmentRequest $request) {
        try {
            return MessageService::getInstance()->uploadAttachment($request->system_upload, $request->channel_uuid);
        } catch (Exception $e) {
            return MessageService::send500($e->getMessage());
        }
    }
    
    public function uploadMultipleFile(MessageMultipleAttachmentRequest $request) {
        try {
            return MessageService::send200(MessageService::getInstance()
                ->uploadMultipleAttachment($request->system_upload, $request->channel_uuid));
        } catch (Exception $e) {
            return MessageService::send500($e->getMessage());
        }
    }
    
    public function downloadAttachment(Request $request) {
        $downloadUrl = MessageService::getInstance()
            ->downloadFile($request->attachment_id, ($request->download ? $request->download : 1));
        if ($downloadUrl != NULL) {
            return redirect($downloadUrl);
        } else {
            $error = 'File doesn`t exist !';
            return view('errors.not_found', ['error' => $error]);
        }
    }

// end of new created method
    
    //todo check
    
    public function getUserByIds(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids'   => 'required|array',
                'user_ids.*' => 'exists:tenant.users,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()
                    ->all())], 422);
            }
            
            $users = User::whereIn('id', $request->user_ids)->get(['id', 'fname', 'lname', 'avatar']);
            return new UserCollection($users);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    // todo check
    public function getUserByWorkshopId(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'workshop_id' => [
                    'required',
                    Rule::exists('tenant.workshops', 'id')->where(function ($query) {
                        (new Workshop())->scopeQualification($query);
                    }),
                ],
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()
                    ->all())], 422);
            }
            
            $users = User::whereIn('id', function ($query) use ($request) {
                $query->select('user_id')
                    ->from('workshop_metas')->where('workshop_id', $request->workshop_id);
            })->get(['id', 'fname', 'lname', 'avatar']);
            return new UserCollection($users);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function storePersonalMessage(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'messages_text' => 'required|min:3',
                'from_id'       => 'required|exists:tenant.users,id',
                'to_id'         => 'required|exists:tenant.users,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()
                    ->all())], 422);
            }
            
            $personalMessage = Message::create(
                [
                    'visitor_ip'    => $request->ip(),
                    'user_id'       => $request->from_id,
                    'messages_text' => $request->messages_text,
                    'to_id'         => $request->to_id,
                    'type'          => 2,
                ]
            );
            return response()->json(['status' => TRUE, 'data' => $personalMessage], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getPersonalConversations(Request $request) {
        try {
            $messages = Message::where(function ($a) {
                $a->orWhere('user_id', Auth::user()->id);
                $a->orWhere('to_id', Auth::user()->id);
            })->groupBy('id')->get(['id', 'user_id', 'to_id']);
            $ids = $messages->pluck('user_id')->unique()->merge($messages->pluck('to_id')->unique())->filter();
            $users = User::whereIn('id', $ids)
                ->where('id', '!=', Auth::user()->id)
                ->get(['id', 'fname', 'lname', 'avatar']);
            return new UserCollection($users);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getPersonalMessages(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:tenant.users,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()
                    ->all())], 422);
            }
            $messages = Message::withCount('messageReplies')->where(function ($a) use ($request) {
                $a->orWhere('user_id', $request->user_id);
                $a->orWhere('to_id', $request->user_id);
            })->get(['id', 'messages_text', 'user_id', 'to_id', 'updated_at', 'created_at']);
            return new MessageCollection($messages);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function sendMessage(SendWorkshopMessageRequest $request) {
        
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $message = Message::create([
                'workshop_id'   => $request->workshopId,
                'category_id'   => $request->categoryId,
                'messages_text' => $request->message,
                'user_id'       => Auth::user()->id,
                'to_id'         => $request->toId,
                'type'          => $request->type, // 1 for message , 2 for personal message
                'visitor_ip'    => $request->ip(),
            ]);
            DB::connection('tenant')->commit();
            return $message;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function replyToMessage(ReplyMessageRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            $reply = MessageReply::create([
                'message_id' => $request->messageId,
                'reply_text' => $request->message,
                'user_id'    => Auth::user()->id,
                'visitor_ip' => $request->ip(),
                'type'       => Message::find($request->messageId)->type,
            ]);
            DB::connection('tenant')->commit();
            if ($reply)
                return new MessageReplyResource($reply);
            return MessageService::send500();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    public function messageLikeToggle(MessageLikeRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction(); // will only apply models with TenantModel only
            if ($request->messageType == 'message') {
                $workshopId = Message::find($request->messageId)->workshop_id;
            } else {
                $workshopId = MessageReply::find($request->messageId)->message->workshop_id;
            }
            $like = MessageLike::updateOrCreate([
                'message_id'       => ($request->messageType == 'message' ? $request->messageId : NULL),
                'message_reply_id' => ($request->messageType == 'reply' ? $request->messageId : NULL),
                'user_id'          => Auth::user()->id,
            ], [
                'workshop_id'      => $workshopId,
                'message_id'       => ($request->messageType == 'message' ? $request->messageId : NULL),
                'message_reply_id' => ($request->messageType == 'reply' ? $request->messageId : NULL),
                'user_id'          => Auth::user()->id,
                'status'           => $request->status,
            ]);
            DB::connection('tenant')->commit();
            if ($like)
                return new MessageLikeResource($like);
            return MessageService::send500('Internal Server Error for making like connection to message');
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return MessageService::send500($e->getMessage());
        }
    }
    
    public function messageStarToggle(MessageStarRequest $request) {
        try {
            $messageStar = MessagesStar::where([
                'message_id' => $request->messageId,
                'user_id'    => Auth::user()->id,
            ]);
            if ($request->status == 0) {
                if ($messageStar->count())
                    $messageStar->delete();
                $msg = $star = 'Removed Star';
            } else {
                $star = MessagesStar::updateOrCreate([
                    'message_id' => $request->messageId,
                    'user_id'    => Auth::user()->id,
                ], [
                    'message_id' => $request->messageId,
                    'user_id'    => Auth::user()->id,
                ]);
                $msg = 'Stared';
            }
            if ($star) {
                return MessageService::send200($msg);
            }
            return MessageService::send500('Internal Server Error');
        } catch (\Exception $e) {
            return MessageService::send500($e->getMessage());
        }
    }
    
    
}
