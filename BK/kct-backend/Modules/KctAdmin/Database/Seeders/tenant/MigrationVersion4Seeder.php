<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\EventUserJoinReport;

class MigrationVersion4Seeder extends Seeder {
    use ServicesAndRepo;

    private ?Group $defaultGroup;

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run() {
        Model::unguard();
        $this->eventRecurrenceUuidMigrate();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To migrate the event recurrence data for version 4 here the event recurrence uuid will be created
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function eventRecurrenceUuidMigrate() {
        $events = Event::whereDoesntHave('eventRecurrenceRecord')->get();
        foreach ($events as $event) {
            $event->eventRecurrenceRecord()->create([
                'recurrence_count' => 1,
                'recurrence_date'  => $event->start_time,
            ]);
        }
    }
}
