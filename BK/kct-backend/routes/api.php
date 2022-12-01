<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\KctAdmin\Console\RecurEvents;
use Modules\KctAdmin\Database\Seeders\tenant\SyncDummyUsersTableSeeder;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventRecurrences;
use Modules\KctAdmin\Entities\EventSingleRecurrence;
use Modules\KctAdmin\Entities\EventUser;
use Modules\KctAdmin\Http\Controllers\V1\EventController;
use Modules\KctAdmin\Repositories\factory\EventRepository;
use When\When;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/recur/updateEvent', [EventController::class, 'updateRecurEvent']);
Route::post('/recur/createTestEvent', [EventController::class, 'createTestEvents']);
Route::post('/recur/deleteTestEvent', [EventController::class, 'deleteTestEvents']);

Route::get('rec/execute', function () {
    return (new RecurEvents())->handle();
});

Route::get('/updateEventTime', function () {
    $event = Event::find('5ab3b5f8-3335-11ed-94d4-38f3ab76fa54');
    $event->start_time = Carbon::now()->addSeconds(20);
    $event->end_time = Carbon::now()->addHour();
    $moments = $event->moments;
    foreach ($moments as $moment) {
        $moment->start_time = Carbon::now()->addMinutes(1);
        $moment->end_time = Carbon::now()->addHour();
        $moment->update();
    }
    $event->update();
    return $event;
});

Route::get('testAnalytics', function (Request $request) {

    DB::connection('tenant')->beginTransaction();
    $carbon = Carbon::now();
    $eventId = $request->input('event_uuid', 'e9e9e4d2-42f7-11ed-b076-38f3ab76fa54');

    $event = Event::find($eventId);
    $eventStart = Carbon::make($event->start_time);
    $eventEnd = Carbon::make($event->end_time);

    $recStart = $carbon->clone()->subDays(30);
    $recEnd = $carbon->clone()->addDays(30);

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
    //                                  EVENT RECURRENCE SECTION
    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------

    EventRecurrences::where('event_uuid', $eventId)->update([
        'start_date' => $recStart->toDateString(),
        'end_date'   => $recEnd->toDateString(),
    ]);


    $r = new When();
    $r->startDate($recStart)
        ->freq("daily")
        ->interval($event->eventRecurrenceData->recurrences_settings['repeat_interval'])
        ->until($recEnd)
        ->generateOccurrences();

    $occurrences = [];
    if (count($r->occurrences)) {
        foreach ($r->occurrences as $occurrence) {
            if ($occurrence->toDateString() <= $carbon->toDateString()) {
                $occurrences[] = $occurrence->setTimeFrom($eventStart);
            }
        }
    }


    EventSingleRecurrence::where('event_uuid', $eventId)->delete();

    foreach ($occurrences as $key => $occurrence) {
        EventSingleRecurrence::create([
            'event_uuid'       => $eventId,
            'recurrence_date'  => $occurrence->toDateTimeString(),
            'recurrence_count' => $key + 1,
        ]);
    }


    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
    //                                  EVENT USER REGISTRATION
    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------

    $employee = User::role('employee')->get();
    $manager = User::role('manager')->get();
    $executive = User::role('executive')->get();
    $other = User::role('other')->get();

    $eventRepo = new EventRepository();

    EventUser::whereIn('user_id', $employee->merge($manager)->merge($executive)->merge($other)->pluck('id'))->delete();

    $update = function ($userId) use ($eventId) {
        $eventRepo = new EventRepository();
        $eventUser = $eventRepo->addUserToEvent($eventId, $userId);
        $eventUser->is_joined_after_reg = 1;
        $eventUser->update();
    };

    foreach ($employee as $e) {
        $update($e->id);
    }
    foreach ($manager as $e) {
        $eventUser = $eventRepo->addUserToEvent($eventId, $e->id);
        $eventUser->is_joined_after_reg = 1;
        $eventUser->update();
    }
    foreach ($executive as $e) {
        $eventUser = $eventRepo->addUserToEvent($eventId, $e->id);
        $eventUser->is_joined_after_reg = 1;
        $eventUser->update();
    }
    foreach ($other as $e) {
        $eventUser = $eventRepo->addUserToEvent($eventId, $e->id);
        $eventUser->is_joined_after_reg = 1;
        $eventUser->update();
    }

    $lastRecurrence = ($event->eventRecurrenceRecord()->orderBy('recurrence_count', 'desc')->limit(1)->first());
    $regCount = $lastRecurrence->actionLog->reg_count;
    $totalTargetUser = $employee->count() + $manager->count() + $executive->count() + $other->count();
    $lastRecurrence->actionLog->reg_count = $regCount >= $totalTargetUser ? $regCount - $totalTargetUser : 1;
    $lastRecurrence->actionLog->update();


    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
    //                                  LOGS FOR EVENT USERS REGISTRATION
    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------

    // second last event recurrence modification
    $eventRec2 = $event->eventRecurrenceRecord()->orderBy('recurrence_date', 'desc')->offset(2)->limit(1)->first();
    $eventRec1 = $event->eventRecurrenceRecord()->orderBy('recurrence_date', 'desc')->offset(1)->limit(1)->first();

    $eventRec2Employee = 10;
    $eventRec2Manager = 5;
    $eventRec2Executive = 2;
    $eventRec2Other = 15;

    // 10 Employee, 5 manager, 2 Executive, 7 Others
    $eventRec2->actionLog->reg_count =
        $eventRec2Employee
        + $eventRec2Manager
        + $eventRec2Executive
        + $eventRec2Other;
    $eventRec2->actionLog->update();

    // 10 Employee, 5 manager, 2 Executive, 7 Others
    $eventRec1->actionLog->reg_count = $employee->skip($eventRec2Employee)->count() // 15
        + $employee->skip($eventRec2Manager)->count()  // 20
        + $employee->skip($eventRec2Executive)->count()
        + $employee->skip($eventRec2Other)->count();
    $eventRec1->actionLog->update();

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
    //                                 Event User Join/Attendance ADD
    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------

    $eventRec2AttendanceEmployee = $eventRec2Employee - 1;
    $eventRec2AttendanceManager = $eventRec2Manager - 2;
    $eventRec2AttendanceExecutive = $eventRec2Executive;
    $eventRec2AttendanceOther = $eventRec2Other - 5;

    $eventRec2StartDate = Carbon::make($eventRec2->recurrence_date);

    $event->eventJoinedReport()->delete();


    $markAttendance = function ($users, $usersLimit, $eventRecStart) use ($eventStart, $event) {
        foreach ($users as $k => $e) {
            $userId = $e->id;
            if ($k >= $usersLimit) break;
            $attendances = rand(1, 5);
            $start = rand(1, 1000);
            $stayFrom = $eventStart->clone()
                ->setDate($eventRecStart->year, $eventRecStart->month, $eventRecStart->day)
                ->addSeconds($start);
            $stayTill = $stayFrom->clone();
            for ($i = 0; $i < $attendances; $i++) {
                $stayFrom = $stayTill->addSeconds(rand(1, 2000));
                $stayTill = $stayFrom->clone();
                $stayTillDuration = rand(100, 3000);
                $stayTill = $stayTill->addSeconds($stayTillDuration);
                $event->eventJoinedReport()->create([
                    'user_id'    => $userId,
                    'created_at' => $stayFrom,
                    'on_leave'   => $stayTill
                ]);
            }
        }
    };

    $markAttendance($employee, $eventRec2AttendanceEmployee, $eventRec2StartDate);
    $markAttendance($manager, $eventRec2AttendanceManager, $eventRec2StartDate);
    $markAttendance($executive, $eventRec2AttendanceExecutive, $eventRec2StartDate);
    $markAttendance($other, $eventRec2AttendanceOther, $eventRec2StartDate);

    $eventRec2->actionLog->attendee_count = $eventRec2AttendanceEmployee
        + $eventRec2AttendanceManager
        + $eventRec2AttendanceExecutive
        + $eventRec2AttendanceOther;
    $eventRec2->actionLog->update();

    $eventRec1AttendanceEmployee = $employee->count() - 10;
    $eventRec1AttendanceManager = $manager->count() - 4;
    $eventRec1AttendanceExecutive = $executive->count() - 10;
    $eventRec1AttendanceOther = $other->count() - 20;

    $eventRec1StartDate = Carbon::make($eventRec2->recurrence_date);

    $markAttendance($employee, $eventRec1AttendanceEmployee, $eventRec1StartDate);
    $markAttendance($manager, $eventRec1AttendanceManager, $eventRec1StartDate);
    $markAttendance($executive, $eventRec1AttendanceExecutive, $eventRec1StartDate);
    $markAttendance($other, $eventRec1AttendanceOther, $eventRec1StartDate);

    $eventRec1->actionLog->attendee_count = $eventRec1AttendanceEmployee
        + $eventRec1AttendanceManager
        + $eventRec1AttendanceExecutive
        + $eventRec1AttendanceOther;
    $eventRec1->actionLog->update();

    $lastRecurrence->actionLog->attendee_count = rand(0, $totalTargetUser - 1);
    $lastRecurrence->actionLog->update();

    DB::connection('tenant')->commit();
    return $carbon->toDateTimeString();

});


Route::get('testDummy2', function (Request $request) {

    $sync = new SyncDummyUsersTableSeeder();
    $sync->run();


});


