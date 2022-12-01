<?php

namespace Modules\KctAdmin\Console;

use Carbon\Carbon;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\EventUserJoinReport;
use Modules\KctUser\Entities\LogEventConversation;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SyncLogsV4 extends Command {

    use ServicesAndRepo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate the logs for the previous events data';

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
     *
     * @return mixed
     */
    public function handle() {

        $webSites = Website::all();

        $result = [];

        foreach ($webSites as $website) {
            $this->adminServices()->superAdminService->setTenant($website);
            DB::connection('tenant')->beginTransaction();
            $this->syncEventUserJoinReports();
            $this->syncConversationData();
            DB::connection('tenant')->commit();
        }
    }

    public function syncEventUserJoinReports() {
        $joins = EventUserJoinReport::with(['event.eventRecurrenceRecord' => function ($q) {
            $q->selectRaw("*, DATE(recurrence_date) as rec_date");
        }])->whereHas('event')->get();


        foreach ($joins as $join) {
            $rec = $join->event->eventRecurrenceRecord->filter(function ($rec) use ($join) {
                return $rec->rec_date === $join->created_at->toDateString();
            })->first();
            $join->recurrence_uuid = $rec ? $rec->recurrence_uuid : null;
            $join->update();
        }
    }

    public function syncConversationData() {
        $logs = LogEventConversation::with(['space.event.eventRecurrenceRecord' => function ($q) {
            $q->selectRaw("*, DATE(recurrence_date) as rec_date");
        }])->whereHas('space.event.eventRecurrenceRecord')->get();
        foreach ($logs as $log) {
            $carbon = Carbon::make($log->convo_start);
            $rec = $log->space->event->eventRecurrenceRecord->filter(function ($rec) use ($carbon) {
                return $rec->rec_date === $carbon->toDateString();
            })->first();
            if (!$rec) {
                continue;
            }
            $log->rec_uuid = $rec->recurrence_uuid;
            $log->update();
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
