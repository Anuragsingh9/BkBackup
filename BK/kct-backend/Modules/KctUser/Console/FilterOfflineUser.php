<?php

namespace Modules\KctUser\Console;

use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Entities\EventUserJoinReport;
use Modules\KctUser\Events\ConversationLeaveEvent;
use Modules\KctUser\Traits\Services;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FilterOfflineUser extends Command {

    use Services;
    use ServicesAndRepo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversation:filter-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $webSites = Website::all();
        $result = [];
        foreach ($webSites as $website) {
            $this->adminServices()->superAdminService->setTenant($website);
            try {
                DB::connection('tenant')->beginTransaction();

                $waitTime = 1; //min
                $now = Carbon::now();

                $events = Event::where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now->subMinutes($waitTime + 1))->get();

                $targetUser = [];

                foreach ($events as $event) {
                    $onlineUsers = Redis::lrange("KCT_EVT_USRS_{$event->event_uuid}", 0, -1);
                    $spacesId = $event->spaces->pluck('space_uuid');

                    $offlineUserWithConversation = EventSpaceUser::whereIn('space_uuid', $spacesId)
                        ->whereHas('conversation')
                        ->whereNotIn('user_id', $onlineUsers)
                        ->whereNotNull('current_conversation_uuid')
                        ->get();
                    foreach ($offlineUserWithConversation as $offlineUser) {
                        $userJoin = EventUserJoinReport::where('event_uuid', $event->event_uuid)
                            ->where('user_id', $offlineUser->user_id)
                            ->orderBy('id', 'desc')
                            ->first();

                        if ($userJoin && $userJoin->on_leave) {
                            $leaveOn = Carbon::make($userJoin->on_leave);
                            if ($now->diffInMinutes($leaveOn) > $waitTime) {
                                $offlineUser->event_uuid = $event->event_uuid;
                                $targetUser[] = $offlineUser;
                            }
                        }
                    }
                }

                foreach ($targetUser as $removeUser) {
                    $removeUser->load('conversation');
                    $conversation = $removeUser->conversation;
                    $actionType = 'remove';
                    if ($this->userServices()->spaceService->getConversationUserCount($conversation) <= 2) {
                        // if there are only two users left in conversation and
                        // one left remove both from conversation and delete conversation
                        $this->userServices()->spaceService->deleteConversation($conversation);
                        $actionType = 'delete';
                    } else {
                        $this->userServices()->spaceService->deleteUsersFromConversation($conversation, [$removeUser->user_id]);
                        $this->userServices()->kctService->handleHostLeave($conversation, $removeUser->user_id);
                    }

                    // conversation not deleted, there are still more than 1 user left in conversation
                    $conversation = $this->userServices()->kctService->mapDummyUsersToConv($conversation);
                    // validate if only dummy users left in conversation then destroy the conversation
                    $this->userServices()->kctService->validateRealUsersInConversation($conversation);

                    event(new ConversationLeaveEvent([
                        'userId'         => $removeUser->user_id,
                        'spaceId'         => $removeUser->space_uuid,
                        'eventId'         => $removeUser->event_uuid,
                        'conversationId' => $removeUser->current_conversation_uuid,
                        'actionType'     => $actionType,
                    ]));
                }

                DB::connection('tenant')->commit();

            } catch (Exception $e) {

            }
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
