<?php


namespace Modules\Messenger\Service;


use App\Http\Controllers\CoreController;
use App\Topic;
use http\Env\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Messenger\Entities\Channel;
use Modules\Messenger\Entities\Message;
use Modules\Messenger\Entities\MessageMedia;
use Modules\Messenger\Entities\MessageReaction;
use Modules\Messenger\Entities\MessageReply;
use Modules\Messenger\Entities\WorkshopTopic;

class MessageService {
    /**
     * @return MessageService
     */
    public static function getInstance() {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    
    public static function send500($msg = '') {
        if ($msg)
            return response()->json(['status' => FALSE, 'msg' => $msg], 500);
        return response()->json(['status' => FALSE, 'msg' => __('message.IM.internalServerError')], 500);
    }
    
    /**
     * @param string $data
     * @return JsonResponse
     */
    public static function send200($data = '') {
        return response()->json(['status' => TRUE, 'data' => $data], 200);
    }
    
    /**
     * @param $param
     * @return Message|null
     * @throws Exception
     */
    public function sendMessage($param) {
        $message = Message::create($param);
        if (!$message)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        if ($param['url']) {
            $mediaParam = array_map(function ($r) use ($message) {
                return [
                    'attachmentable_id'   => $message->id,
                    'attachmentable_type' => Message::class,
                    'media_url'           => $r['url'],
                    'source'              => $r['type'],
                    'title'               => (isset($r['file_name']) ? $r['file_name'] : NULL),
                ];
            }, $param['url']);
            $media = MessageMedia::insert($mediaParam);
        }
        return $message;
    }
    
    /**
     * @param $messageId
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function updateMessage($messageId, $param) {
        $message = Message::withCount('replies')->find($messageId);
        $update = $message->update($param);
        if (!$update)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return $message;
    }
    
    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function replyMessage($param) {
        $reply = MessageReply::create($param);
        if (!$reply)
            throw new Exception();
        if ($param['url']) {
            $mediaParam = array_map(function ($r) use ($reply) {
                return [
                    'attachmentable_id'   => $reply->id,
                    'attachmentable_type' => MessageReply::class,
                    'media_url'           => $r['url'],
                    'source'              => $r['type'],
                    'title'               => (isset($r['file_name']) ? $r['file_name'] : NULL),
                ];
            }, $param['url']);
            $media = MessageMedia::insert($mediaParam);
        }
        return $reply;
    }
    
    /**
     * @param $param
     * @param $replyId
     * @return mixed
     * @throws Exception
     */
    public function replyUpdate($param, $replyId) {
        $reply = MessageReply::find($replyId);
        if (!$reply->update($param))
            throw new Exception();
        return $reply;
    }
    
    /**
     * @param $replyId
     * @return mixed
     * @throws Exception
     */
    public function replyDelete($replyId) {
        $reply = MessageReply::find($replyId);
        if (!$reply->delete())
            throw new Exception();
        return TRUE;
    }
    
    /**
     * @param $messageId
     * @return bool
     * @throws Exception
     */
    public function delete($messageId) {
        $message = Message::find($messageId);
        if (!$message->delete())
            throw new Exception();
        return TRUE;
    }
    
    /**
     * @param UploadedFile $file
     * @param $channelUuid
     * @return mixed
     */
    public function uploadAttachment($file, $channelUuid) {
        $filePath = '/messenger';
        
        $channel = Channel::find($channelUuid); // find that channel
        $topic = WorkshopTopic::where('channel_uuid', $channelUuid)->first();
        
        if ($channel->channel_type == 1 && $topic && $topic->workshop_id) // if channel is of topic then add workshop id to path
            $filePath .= '/workshop/' . $topic->workshop_id;
        
        $filePath .= "/" . $channelUuid;
        $core = app(\App\Http\Controllers\CoreController::class);
        return [
            'url'       => $core->fileUploadByS3($file, $filePath, 'private'),
            'ext'       => $file->getClientOriginalExtension(),
            'file_name' => $file->getClientOriginalName(),
        ];
//        $s3 = Storage::disk('s3');
//        return Storage::disk('s3')->putFile($filePath, $file, 'public');
//        $s3->put('/' . $filePath, file_get_contents($file), 'public');
//        return $s3->url($filePath);
    }
    
    public function uploadMultipleAttachment($files, $channelUuid) {
        $core = app(\App\Http\Controllers\CoreController::class);
        $filePath = '/messenger';
        
        $channel = Channel::find($channelUuid); // find that channel
        $topic = WorkshopTopic::where('channel_uuid', $channelUuid)->first();
        if ($channel->channel_type == 1 && $topic && $topic->workshop_id) // if channel is of topic then add workshop id to path
            $filePath .= '/workshop/' . $topic->workshop_id;
        $urls = [];
        foreach ($files as $file) {
            $filePath .= "/" . $channelUuid;
//            return $filePath;
            $urls[] = [
                'url'       => $core->fileUploadByS3($file, $filePath, 'private'),
                'ext'       => $file->getClientOriginalExtension(), // prepare file name and put time so don't conflict
                'file_name' => $file->getClientOriginalName(),
            ];
        }
        return $urls;
    }
    
    /**
     * @param $param
     * @param integer $messageId
     * @return mixed
     * @throws Exception
     */
    public function toggleReaction($param, $messageId) {
        $reaction = MessageReaction::where($param);
        if ($reaction->count()) {  // if count is there that means to unlike or un-star
            if (!$reaction->delete())
                throw new Exception();
        } else { // else toggle is to make like or star
            $reaction = MessageReaction::create($param, $param);
            if (!$reaction)
                throw new Exception();
        }
        return Message::withCount('isStared')->withCount('likes')->withCount('replies')->find($messageId);
    }
    
    /**
     * @param $messageId
     * @return mixed
     * get replies of messages
     */
    public function getReplies($messageId) {
        return MessageReply::where('message_id', $messageId)
            ->orderBy('id', 'asc')
            ->get();
    }
    
    public function downloadFile($attachmentId, $mode) {
        $media = MessageMedia::find($attachmentId);
        if ($media) {
            $core = app(\App\Http\Controllers\CoreController::class);
            $fileName = $media->title;
            return $core->getS3Parameter($media->media_url, $mode, $fileName);
        }
    }
}
