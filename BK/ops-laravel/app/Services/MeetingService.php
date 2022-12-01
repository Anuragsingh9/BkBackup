<?php


    namespace App\Services;

    use App\Events\MeetingEvent;
    use App\Mail\MeetingEmail;
    use App\Http\Resources\MeetingDateCollection;
    use App\Meeting;
    use App\MeetingMeta;
    use App\Services\BlueJeans\Model\BlueJeansMeeting;
    use App\Services\BlueJeans\Model\BlueJeansUser;
    use App\Setting;
    use App\UserBlueJeans;
    use App\User;
    use App\Workshop;
    use App\WorkshopMeta;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Validator;
    use Modules\Events\Emails\SendUserRegisterEmail;
    use App;
    use Modules\Resilience\Entities\ConsultationStepMeeting;
    use App\DoodleDates;

    class MeetingService
    {
        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }

        public static function getInstance()
        {
            static $instance = NULL;
            if (NULL === $instance) {
                $instance = new static();
            }
            return $instance;
        }

        public function createMeeting($params)
        {
            $validator = Validator::make($params, [
                'name'              => 'required|string|max:255',
                'description'       => 'nullable|string|max:3000',
                'place'             => 'required|string|max:255',
                'image'             => 'nullable|image',
                'date'              => 'required|date|after:yesterday',
                'start_time'        => 'required',
                'end_time'          => 'required',   // todo after start
                'meeting_date_type' => 'nullable|integer|in:0,1',
                'meeting_type'      => 'nullable|integer|in:1,2,3',
                'workshop_id'       => 'required|integer', // do not add exists as it would give not exists in single transaction
                'user_id'           => 'required|exists:tenant.users,id',
                'visibility'        => 'nullable|integer',
                'status'            => 'nullable|in:0,1',
                'is_offline'        => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                DB::connection('tenant')->beginTransaction();
                $meetingData = [
                    'name'                       => $params['name'],
                    'code'                       => (isset($params['code']) ? $params['code'] : NULL),
                    'description'                => (isset($params['description']) ? $params['description'] : NULL),
                    'place'                      => (isset($params['place']) ? $params['place'] : NULL),
                    'mail'                       => (isset($params['mail']) ? $params['mail'] : NULL),
                    'contact_no'                 => (isset($params['contact_no']) ? $params['contact_no'] : NULL),
                    'image'                      => (isset($params['image']) ? $params['image'] : NULL),
                    'header_image'               => (isset($params['header_image']) ? $params['header_image'] : NULL),
                    'lat'                        => (isset($params['lat']) ? $params['lat'] : NULL),
                    'long'                       => (isset($params['long']) ? $params['long'] : NULL),
                    'date'                       => $params['date'],
                    'start_time'                 => $params['start_time'],
                    'end_time'                   => $params['end_time'],
                    'meeting_date_type'          => (isset($params['meeting_date_type']) ? $params['meeting_date_type'] : 1),
                    'meeting_type'               => (isset($params['meeting_type']) ? $params['meeting_type'] : 1),
                    'workshop_id'                => (isset($params['workshop_id']) ? $params['workshop_id'] : NULL),
                    'user_id'                    => $params['user_id'],
                    'visibility'                 => (isset($params['visibility']) ? $params['visibility'] : 0),
                    'status'                     => (isset($params['status']) ? $params['status'] : 1),
                    'prepd_published_on'         => (isset($params['prepd_published_on']) ? $params['prepd_published_on'] : NULL),
                    'repd_published_on'          => (isset($params['repd_published_on']) ? $params['repd_published_on'] : NULL),
                    'prepd_published_by_user_id' => (isset($params['prepd_published_by_user_id']) ? $params['prepd_published_by_user_id'] : NULL),
                    'repd_published_by_user_id'  => (isset($params['repd_published_by_user_id']) ? $params['repd_published_by_user_id'] : NULL),
                    'validated_prepd'            => (isset($params['validated_prepd']) ? $params['validated_prepd'] : 0),
                    'validated_repd'             => (isset($params['validated_repd']) ? $params['validated_repd'] : 0),
                    'redacteur'                  => (isset($params['redacteur']) ? $params['redacteur'] : NULL),
                    'is_offline'                 => (isset($params['is_offline']) ? $params['is_offline'] : 0),
                    'is_downloaded'              => (isset($params['is_downloaded']) ? $params['is_downloaded'] : 0),
                    'is_prepd_final'             => (isset($params['is_prepd_final']) ? $params['is_prepd_final'] : 0),
                    'is_repd_final'              => (isset($params['is_repd_final']) ? $params['is_repd_final'] : 0),
                    'with_meal'                  => (isset($params['with_meal']) ? $params['with_meal'] : 0),
                    'is_import'                  => (isset($params['is_import']) ? $params['is_import'] : 0),
                ];
                $createdMeetingId = Meeting::insertGetId($meetingData);
                DB::connection('tenant')->commit();
                return $createdMeetingId;
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                return $e->getMessage();
            }
        }

        /**
         * - To assign a bluejeans user to the meeting
         * - this method will auto allocate the available user at that date and time,
         *
         * @param $param
         * @param $meetingId
         * @return JsonResponse|null
         * @throws App\Exceptions\CustomValidationException
         */
        public function registerBlueJeansUser($param, $meetingId)
        {
            $userId = NULL;
            if ($param->meeting_date_type) { // single date
                $users = $this->getAvailableBlueJeansUser($param);
                if ($users === NULL) {
                    throw new App\Exceptions\CustomValidationException(__('message.no_bluejeans_available'));
                }
                if ($users->count()) {
                    $userId = $users->first()->bluejeans_user_id;
                }
            } else { // doodle date so checking each date time of doodle have available user.
                if ($param->meetingDates && count(json_decode($param->meetingDates)))
                    $dateWiseAvailableUser = [];
                $pivot = 0;
                foreach (json_decode($param->meetingDates) as $doodle) {
                    $users = $this->getAvailableBlueJeansUser($doodle);
                    if (!$users->count()) return response()->json(['status' => FALSE, 'msg' => 'no user found for (' . $doodle->date . ') and (' . $doodle->s_time . '-' . $doodle->e_time . ') '], 422);
                    $dateWiseAvailableUser[] = $users->pluck('bluejeans_user_id')->toArray();
                    $pivot += 1;
                }
                if ($pivot > 1)
                    $availableUser = call_user_func_array('array_intersect', $dateWiseAvailableUser);
                else if ($pivot == 1)
                    $availableUser = $dateWiseAvailableUser[0];
                else
                    return response()->json(['status' => FALSE, 'msg' => 'no common video user found for the dates provided'], 422);
                $userId = $availableUser[0];
            }
            if (!$userId) {
                return NULL;
            }
            $meetingMeta = MeetingMeta::create([
                'meeting_id'               => $meetingId,
                'video_meeting_id'         => NULL,
                'video_meeting_numeric_id' => NULL,
                'video_meeting_user_id'    => $userId,
            ]);

            if ($meetingMeta)
                return $meetingMeta;
            return NULL;
        }

        // Send validated data here
        public function createVideoMeeting($param, $meetingId)
        {
            $userId = MeetingMeta::where('meeting_id', $meetingId);
            if (!$userId->count()) return NULL;
            $createParam = [
                'title'            => $param->name,
                'description'      => $param->description,
                'start'            => \DateTime::createFromFormat('Y-m-d H:i:s', $param->date . ' ' . $param->start_time)
                    ->format('U'),
                'end'              => \DateTime::createFromFormat('Y-m-d H:i:s', $param->date . ' ' . $param->end_time)
                    ->format('U'),
                'timezone'         => Carbon::now()->timezoneName,
                'attendees'        => [],
                'isLargeMeeting'   => TRUE,
                'personal_meeting' => FALSE,
                'userId'           => $userId->first()->video_meeting_user_id,
            ];
            $meeting = BlueJeansMeeting::create($createParam);
            if (!$meeting) {
                return NULL;
            }
            $meetingMeta = $userId->update([
                'video_meeting_id'         => $meeting->id,
                'video_meeting_numeric_id' => $meeting->numericMeetingId,
            ]);
            if ($meetingMeta) {
                return $meetingMeta;
            }
            return NULL;
        }

        public function accessVideoMeeting($meeting)
        {
            if (!in_array(Auth::user()->role, ['M0', 'M1'])) {
                $workshopMeta = WorkshopMeta::where([
                    'workshop_id' => $meeting->workshop_id,
                    'user_id'     => Auth::user()->id,
                ]);
                if (!$workshopMeta->count()) {
                    return NULL;
                }
                $workshopMeta = $workshopMeta->first();
                $isWorkshopAdmin = $workshopMeta->role == 1 || $workshopMeta->role == 2;
            } else {
                $isWorkshopAdmin = TRUE; //
            }

            $meetingMeta = MeetingMeta::where('meeting_id', $meeting->id)->first();
            if (isset($meetingMeta->video_meeting_user_id) && $isWorkshopAdmin) { // Secretory or deputy
                // $videoUser = BlueJeansUser::find($meetingMeta->video_meeting_user_id);
                $videoUser = BlueJeansUser::find($meetingMeta->video_meeting_user_id, TRUE);

                if (!$videoUser) {
                    return NULL;
                }
                $moderatorPasscode = $videoUser->moderatorPasscode;
                // generate moderator link; like https://bluejeans.com/981509113/3422944213/webrtc
                return 'https://bluejeans.com/' . $meetingMeta->video_meeting_numeric_id . '/' . $moderatorPasscode . '/webrtc';
            } else {
                if (isset($meetingMeta->video_meeting_id)) {
//                    $videoMeeting = BlueJeansMeeting::find($meetingMeta->video_meeting_id);
                    $videoMeeting = BlueJeansMeeting::find(['meetingId' => $meetingMeta->video_meeting_id, 'userId' => $meetingMeta->video_meeting_user_id]);
                }
                if (!isset($videoMeeting) || !$videoMeeting) {
                    return NULL;
                }
                return 'https://bluejeans.com/' . $meetingMeta->video_meeting_numeric_id . '/' . $videoMeeting->attendeePasscode . '/webrtc';
            }
        }

        public function updateVideoMeeting($param, $meetingId)
        {
            $meetingMeta = MeetingMeta::where('meeting_id', $meetingId)->first();
            if (!$meetingMeta) {
                return NULL;
            }

            $updateFields = [
                'meetingId'   => $meetingMeta->video_meeting_id,
                'userId'      => $meetingMeta->video_meeting_user_id,
                'title'       => $param->name,
                'description' => $param->description,
                'start'       => \DateTime::createFromFormat('Y-m-d H:i:s', $param->date . ' ' . $param->start_time)
                    ->format('U'),
                'end'         => \DateTime::createFromFormat('Y-m-d H:i:s', $param->date . ' ' . $param->end_time)
                    ->format('U'),
                'timezone'    => Carbon::now()->timezoneName,

            ];
            $update = BlueJeansMeeting::update($updateFields);
            if ($update) {
                return $update;
            }
            return NULL;
        }

        /**
         * @param $meetingId
         * @return int|null
         * this will delete the meeting from bluejeans and meeting meta table.
         */
        public function deleteVideoMeeting($meetingId)
        {
            $meetingMeta = MeetingMeta::where('meeting_id', $meetingId)->first();
            if (!$meetingMeta) {
                return NULL;
            }

            $deleteFields = [
                'meetingId' => $meetingMeta->video_meeting_id,
                'userId'    => $meetingMeta->video_meeting_user_id,
            ];
            $delete = BlueJeansMeeting::delete($deleteFields);
            if ($delete) {
                MeetingMeta::where('meeting_id', $meetingId)->delete();
                return $delete;
            }
            return NULL;
        }

        /**
         * @param $settingKey
         * @param $rawData
         * @return null
         * $rawData['meetingId']
         * $rawData['participantId']
         */
        public function prepareEmailData($settingKey, $rawData)
        {
            $settingKey = $this->setLanguageToSettingKey($settingKey);
            $setting = Setting::where('setting_key', $settingKey)->first();
            if (!$setting) {
                return NULL;
            }
            $meeting = (isset($rawData['meetingId']) ? Meeting::with(['workshop' => function ($q) {
                $q->select('id', 'president_id', 'validator_id', 'workshop_name', 'code1', 'code2');
                $q->with(['meta' => function ($q) {
                    $q->select('id', 'user_id', 'workshop_id', 'role');
                    $q->with(['user' => function ($q) {
                        $q->select('id', 'fname', 'lname', 'email', 'phone', 'role');
                    }]);
                }]);
            }])->find($rawData['meetingId']) : '');
            $workshop = ($meeting && isset($meeting->workshop->id) ? $meeting->workshop : NULL);
            $participant = (isset($rawData['participant']) ? $rawData['participant'] : NULL);
            $link = (isset($rawData['parms']['url']) ? $rawData['parms']['url'] : NULL);
            $token = (isset($rawData['parms']['token']) ? $rawData['parms']['token'] : NULL);
            $tags = [
                '[[WorkshopLongName]]'  => ($workshop ? $workshop->workshop_name : NULL),
                '[[WorkshopShortName]]' => ($workshop ? $workshop->code1 : NULL),

                '[[WorkshopPresidentFullName]]' => ($workshop && isset($workshop->president->id) ? $workshop->president->fname . ' ' . $workshop->president->lname : NULL),
                '[[PresidentEmail]]'            => ($workshop && isset($workshop->president->id) ? $workshop->president->email : NULL),
                '[[PresidentPhone]]'            => ($workshop && isset($workshop->president->id) ? $workshop->president->phone : NULL),

                '[[WorkshopvalidatorFullName]]' => ($workshop && isset($workshop->validator->id) ? $workshop->validator->fname . ' ' . $workshop->validator->lname : NULL),
                '[[ValidatorEmail]]'            => ($workshop && isset($workshop->validator->id) ? $workshop->validator->email : NULL),
                '[[ValidatorPhone]]'            => ($workshop && isset($workshop->validator->id) ? $workshop->validator->phone : NULL),

                '[[WorkshopMeetingName]]'    => ($meeting ? $meeting->name : NULL),
                '[[WorkshopMeetingDate]]'    => ($meeting ? $meeting->date : NULL),
                '[[WorkshopMeetingTime]]'    => ($meeting ? $meeting->start_time : NULL),
                '[[WorkshopMeetingAddress]]' => ($meeting ? $meeting->place : NULL),

                '[[UserFirstName]]' => Auth::user()->fname,
                '[[UserLastName]]'  => Auth::user()->lname,
                '[[UserEmail]]'     => Auth::user()->email,

                '[[ParticipantFN]]' => (isset($participant->fname) ? $participant->fname : NULL),
                '[[ParticipantLN]]' => (isset($participant->lname) ? $participant->lname : NULL),
            ];
            $decode = json_decode($setting->setting_value);
//        $data['text_before_link'] = str_replace(array_keys($tags), array_values($tags), $decode->text_before_link);
//        $data['text_after_link'] = str_replace(array_keys($tags), array_values($tags), $decode->text_after_link);
//        $data['data'] = getEmailSetting(['email_graphic', $settingKey]);
//        $data['tags'] = $tags;

            $mailData['mail']['subject'] = str_replace(array_keys($tags), array_values($tags), $decode->email_subject);
            $mailData['mail']['firstname'] = Auth::user()->fname;
            $mailData['mail']['lastname'] = Auth::user()->lname;
            $mailData['mail']['workshop_data'] = $workshop;
            $mailData['mail']['template_setting'] = $setting->setting_key;
            $mailData['mail']['participant'] = $participant;
            $mailData['mail']['meeting'] = $meeting;
            $mailData['mail']['url'] = $link;
            $mailData['mail']['token'] = $token;
            $mailData['mail']['enable_signature'] = FALSE;
            return $mailData;
        }

        protected function setLanguageToSettingKey($eventType)
        {
            if (App::isLocale('en')) {
                $lang = '_EN';
            } else {
                $lang = '_FR';
            }
            return $eventType . $lang;
        }

        public function sendMeetingMail($toUser, $key, $meetingId)
        {
            try {
                $data = $this->prepareEmailData($key, [
                    'meetingId'   => $meetingId,
                    'participant' => $toUser,
                ]);
                event(new MeetingEvent('email_template.dynamic_workshop_template', $data, Auth::user()->email));
                return view('email_template.dynamic_workshop_template', $data);

            } catch (\Exception $w) {
                return 'error: mail not sent ' . $w->getMessage();
            }
        }

        public
        function fetchMeetingDates(array $data)
        {
            $date = config('constants.Today_DATE')->format('Y-m-d');
            $meeting = Meeting::whereIn('meeting_type', [2, 3])->where(function ($a) use ($data) {
                $a->whereMonth('meetings.date', $data['month']);
                $a->whereYear('meetings.date', $data['year']);
                $a->whereDate('meetings.date', '>=', config('constants.Today_DATE')->format('Y-m-d'));
                $a->orWhereNull('meetings.date');
            })->leftJoin('doodle_dates', 'meetings.id', '=', 'doodle_dates.meeting_id')
                ->select('doodle_dates.date as d_date', 'doodle_dates.start_time  as d_start_time', 'doodle_dates.end_time as d_end_time', 'doodle_dates.meeting_id', 'meetings.id', 'meetings.date', 'meetings.start_time', 'meetings.end_time', DB::raw('1 as isUpdate'))->get()->filter(function ($value, $key) use ($date) {
                    return isset($value->date) ? $value->date : $value->d_date >= $date;
                })->values();
            return new MeetingDateCollection($meeting);
        }

        public function syncBlueJeansUser()
        {
            $users = BlueJeansUser::all();
            if($users == null) {
                throw new App\Exceptions\CustomException("Error in sync users with provided credentials");
            }
            $ids = $users->pluck('id')->toArray();
            $users->map(function ($row) {
                UserBlueJeans::withTrashed()->updateOrCreate(
                    ['bluejeans_user_id' => $row->id],
                    ['fname'              => $row->firstName,
                     'lname'              => $row->lastName,
                     'moderator_passcode' => $row->moderatorPasscode,
                    ]);
            });
            UserBlueJeans::where('id', '>', '0')->delete();
            return UserBlueJeans::whereIn('bluejeans_user_id', $ids)->restore();
        }

        public function checkBluejeansSynced() {
            if(!UserBlueJeans::count()) {
                throw new App\Exceptions\CustomValidationException(__('message.no_bluejeans_user'));
            }
        }
    
        /**
         * To get the available bluejeans users for during specific date and time
         *
         * @param $request
         * @return |null
         * @throws App\Exceptions\CustomValidationException
         */
        public function getAvailableBlueJeansUser($request)
        {
            $request->s_time = ($request->s_time) ? $request->s_time : $request->start_time;
            $request->e_time = ($request->e_time) ? $request->e_time : $request->end_time;

            $this->checkBluejeansSynced();
            
            $meetings = UserBlueJeans::select('id', 'bluejeans_user_id')
                ->whereDoesntHave('meetingMetas', function ($q) use ($request) {
                    $q->whereHas('meeting', function ($q) use ($request) {
                        $q->where(function ($q) use ($request) {
                            $q->where('date', $request->date);
                            $q->where(function ($q) use ($request) {
                                $q->orWhere([['start_time', '<=', $request->s_time], ['end_time', '>=', $request->s_time]]);
                                $q->orWhere([['start_time', '<=', $request->e_time], ['end_time', '>=', $request->e_time]]);
                                $q->orWhere([['start_time', '>=', $request->s_time], ['end_time', '<=', $request->e_time]]);
                            });
                        });
                        $q->orWhere(function ($q) use ($request) {
                            $q->whereNull('date');
                            $q->whereHas('doodleDates', function ($q) use ($request) {
                                $q->where('date', $request->date);
                                $q->where(function ($q) use ($request) {
                                    $q->orWhere([['start_time', '<=', $request->s_time], ['end_time', '>=', $request->s_time]]);
                                    $q->orWhere([['start_time', '<=', $request->e_time], ['end_time', '>=', $request->e_time]]);
                                    $q->orWhere([['start_time', '>=', $request->s_time], ['end_time', '<=', $request->e_time]]);
                                });
                            });
                        });
                    });
                })->get();
            return $meetings->count() ? $meetings : NULL;
        }

        public function getOccupiedDates(Request $request)
        {
            $date = $request->year . '-' . sprintf("%'02d", $request->month);
            $usersCount = UserBlueJeans::count();

            $dateCondition = function ($q) use ($date) {
                $q->where('date', 'like', $date . '%');
                if ($date == date('Y-m')) {
                    $q->where(DB::raw('date(date)'), '>=', date('Y-m-d'));
                }
            };

            $meetingTypeCondition = function ($q) {
                $q->whereIn('meeting_type', [2, 3]);
                $q->where('status', '!=', 0);
            };

            $meetingSelector = function ($q) use ($meetingTypeCondition, $dateCondition) {
                $q->select('id', 'date', 'start_time', 'end_time');
                // to select type of meeting, status not null etc.
                $q->where($meetingTypeCondition);
                /**
                 * - the date filter to match either meeting have or its doodle have provided meeting month and year
                 *
                 * @note meeting and doodle both have same name of column `date`
                 * - that's why we used the same $dateCondition for both.
                 */
                $q->where(function ($q) use ($dateCondition) {
                    $q->where($dateCondition);
                    $q->orWhereHas('doodleDates', $dateCondition);
                });
                $q->with(['doodleDates' => $dateCondition]);
            };

            if (!$usersCount)
                return response()->json(['status' => TRUE, 'data' => ''], 200);
            $meetingOnThisMonth = MeetingMeta::with([
                'blueJeansUser',
                'meeting' => $meetingSelector,
            ])
                ->whereHas('meeting', $meetingSelector)
                ->whereHas('blueJeansUser')
                ->select('id', 'meeting_id', 'video_meeting_user_id')
                ->get();
            $processedMeetings = $this->filterToMonthUserWise($meetingOnThisMonth); // keep doing dd to understand working
            $processedMeetings = $this->removeDatesWhichNotHaveAllUsersMeeting($processedMeetings, $usersCount);
            $finalSlots = $this->findBusyDatesAndTime($processedMeetings, $usersCount);
            return response()->json(['status' => TRUE, 'data' => $this->filterResult($finalSlots)], 200);
        }

        protected function filterToMonthUserWise($meetingOnThisMonth)
        {
            $processedMeetings = [];
            $meetingOnThisMonth->map(function ($row) use (&$processedMeetings) {
                if ($row->meeting->date) {
                    $processedMeetings[$row->meeting->date][$row->blueJeansUser->bluejeans_user_id][] = [
                        'meeting_id' => $row->meeting->id,
                        'date'       => $row->meeting->date,
                        'start_time' => $row->meeting->start_time,
                        'end_time'   => $row->meeting->end_time,
                        'type'       => 'meeting',
                    ];
                } else if ($row->meeting->doodleDates->count()) {
                    foreach ($row->meeting->doodleDates as $doodle) {
                        $processedMeetings[$doodle->date][$row->blueJeansUser->bluejeans_user_id][] = [
                            'meeting_id' => $row->meeting->id,
                            'date'       => $doodle->date,
                            'start_time' => $doodle->start_time,
                            'end_time'   => $doodle->end_time,
                            'type'       => 'doodle',
                        ];
                    }
                }
            });
            return $processedMeetings;
        }

        protected function removeDatesWhichNotHaveAllUsersMeeting($processedMeetings, $usersCount)
        {
            foreach ($processedMeetings as $key => &$day) {
                if (count($day) != $usersCount)
                    unset($processedMeetings[$key]);
            }
            return $processedMeetings;
        }

        protected function findBusyDatesAndTime($data, $usersCount)
        {
            $result = [];
            foreach ($data as $key => $day) { // each day
                $slots =
                    [
                        ["07:00:00", "07:15:00", []], ["07:15:00", "07:30:00", []], ["07:30:00", "07:45:00", []], ["07:45:00", "08:00:00", []],
                        ["08:00:00", "08:15:00", []], ["08:15:00", "08:30:00", []], ["08:30:00", "08:45:00", []], ["08:45:00", "09:00:00", []],
                        ["09:00:00", "09:15:00", []], ["09:15:00", "09:30:00", []], ["09:30:00", "09:45:00", []], ["09:45:00", "10:00:00", []],
                        ["10:00:00", "10:15:00", []], ["10:15:00", "10:30:00", []], ["10:30:00", "10:45:00", []], ["10:45:00", "11:00:00", []],
                        ["11:00:00", "11:15:00", []], ["11:15:00", "11:30:00", []], ["11:30:00", "11:45:00", []], ["11:45:00", "12:00:00", []],
                        ["12:00:00", "12:15:00", []], ["12:15:00", "12:30:00", []], ["12:30:00", "12:45:00", []], ["12:45:00", "13:00:00", []],
                        ["13:00:00", "13:15:00", []], ["13:15:00", "13:30:00", []], ["13:30:00", "13:45:00", []], ["13:45:00", "14:00:00", []],
                        ["14:00:00", "14:15:00", []], ["14:15:00", "14:30:00", []], ["14:30:00", "14:45:00", []], ["14:45:00", "15:00:00", []],
                        ["15:00:00", "15:15:00", []], ["15:15:00", "15:30:00", []], ["15:30:00", "15:45:00", []], ["15:45:00", "16:00:00", []],
                        ["16:00:00", "16:15:00", []], ["16:15:00", "16:30:00", []], ["16:30:00", "16:45:00", []], ["16:45:00", "17:00:00", []],
                        ["17:00:00", "17:15:00", []], ["17:15:00", "17:30:00", []], ["17:30:00", "17:45:00", []], ["17:45:00", "18:00:00", []],
                        ["18:00:00", "18:15:00", []], ["18:15:00", "18:30:00", []], ["18:30:00", "18:45:00", []], ["18:45:00", "19:00:00", []],
                        ["19:00:00", "19:15:00", []], ["19:15:00", "19:30:00", []], ["19:30:00", "19:45:00", []], ["19:45:00", "20:00:00", []],
                        ["20:00:00", "20:15:00", []], ["20:15:00", "20:30:00", []], ["20:30:00", "20:45:00", []], ["20:45:00", "21:00:00", []],
                        ["21:00:00", "21:15:00", []], ["21:15:00", "21:30:00", []], ["21:30:00", "21:45:00", []], ["21:45:00", "22:00:00", []],
                    ];
                foreach ($day as $userId => $user) { // to fetch users of that day have meeting
                    foreach ($user as $meeting) { // to fetch meetings of that user
                        $this->fillSlots($meeting, $slots, $userId);
                    }
                }
                $temp = $this->filterSlots($slots, $usersCount);
                if (count($temp))
                    $result[$key] = $temp;
            }
            return $result;
        }

        protected function fillSlots($meeting, &$slots, $userId)
        {
            foreach ($slots as &$slot) {
                if ($slot[0] >= $meeting['start_time'] && $slot[1] <= $meeting['end_time'])
                    if (isset($slot[2]['' . $userId]))
                        $slot[2]['' . $userId] += 1;
                    else
                        $slot[2]['' . $userId] = 1;
                else if ($slot[0] >= $meeting['end_time'])
                    break;
            }
        }

        protected function filterSlots($slots, $usersCount)
        {
            $result = [];
            foreach ($slots as $slot) {
                if (count($slot[2]) == $usersCount) $result[] = $slot;
            }
            return $result;
        }

        protected function filterResult($data)
        {
            $result = [];
            foreach ($data as $key => $value) {
                $temp = ['date' => $key];
                $start_time = $value[0][0];
                $end_time = $value[0][1];
                if (count($value) == 1) {
                    $result[] = ['date' => $key, 'start_time' => $start_time, 'end_time' => $end_time];
                } else {
                    for ($i = 1; $i < count($value); $i++) {
                        if ($end_time == $value[$i][0] && $i != count($value) - 1) // to make the end as next array end
                            $end_time = $value[$i][1];
                        else { // this means now array time is different or ended
                            if ($i == count($value) - 1 && $end_time == $value[$i][0]) { // that means
                                $end_time = $value[$i][1];
                            }
                            $temp['start_time'] = $start_time;
                            $temp['end_time'] = $end_time;
                            $result[] = $temp;
                            $start_time = $value[$i][0];
                            $end_time = $value[$i][1];
                            if ($i == count($value) - 1 && $temp['end_time'] != $value[$i][1]) {
                                $temp = ['date' => $key];
                                $temp['start_time'] = $start_time;
                                $temp['end_time'] = $end_time;
                                $result[] = $temp;
                            }
                            $temp = ['date' => $key];
                        }
                    }
                }
            }
            return $result;
        }

        /**
         * @param $request
         * @param $meeting
         * @throws \Exception
         * this function change type of meeting and perform action
         * as per that
         */
        public function changeMeetingType($request, $meeting)
        {
            //changing to physical from hybrid or video
            if ($request->meeting_type == 1 && in_array($meeting->meeting_type, [2, 3])) {
                if ($request->meeting_date_type == 1) {
                    $this->deleteVideoMeeting($meeting->id);
                }
                $this->updatePresenceData($meeting->id, $request->meeting_type, $meeting->meeting_type);
            } //changing to hybrid or video from physical
            elseif (in_array($request->meeting_type, [2, 3]) && $meeting->meeting_type == 1 && $request->meeting_date_type == 1) {
                $request->merge([
                    's_time'            => $request->start_time,
                    'e_time'            => $request->end_time,
                ]);
                $checkRes1 = $this->registerBlueJeansUser($request, $meeting->id);
                $checkRes = $this->createVideoMeeting($request, $meeting->id);
                if (empty($checkRes) || empty($checkRes1)) {
                    throw new \Exception('Meeting Not Created');
                }
                $this->updatePresenceData($meeting->id, $request->meeting_type, $meeting->meeting_type);
                if ($request->meeting_type == 2) {
                    $this->changeMeetingStatus($meeting->id);
                }
            } //changing to hybrid or video from physical
            elseif (in_array($request->meeting_type, [2, 3]) && $meeting->meeting_type == 1) {
                $this->updatePresenceData($meeting->id, $request->meeting_type, $meeting->meeting_type);
                if ($request->meeting_type == 2) {
                    $this->changeMeetingStatus($meeting->id);
                }
            }          //changing to video   from hybrid
            elseif ($meeting->meeting_type == 3 && $request->meeting_type == 2) {
                $this->updatePresenceData($meeting->id, $request->meeting_type, $meeting->meeting_type);
                $this->changeMeetingStatus($meeting->id);
            } //changing to hybrid  from video
            elseif ($meeting->meeting_type == 2 && $request->meeting_type == 3) {
                $this->updatePresenceData($meeting->id, $request->meeting_type, $meeting->meeting_type);
            }

        }

        /**
         * @param $meetingId
         * @param $newType
         * @param $oldType
         */
        protected function updatePresenceData($meetingId, $newType, $oldType)
        {
            $presence = App\Presence::where('meeting_id', $meetingId)->get();
            foreach ($presence as $item) {
                if ($item->video_presence_status == (string)0) {
                    $item->video_presence_status = 'AE';
                } elseif ($item->video_presence_status == (string)1) {
                    $item->video_presence_status = $item->presence_status;
                }
                $this->meetingChangeScenario($newType, $oldType, $item);
            }

        }

        public
        function checkMeetingInResilience($meetingId)
        {
            $resMeeting = ConsultationStepMeeting::where('meeting_id', $meetingId);
            return $resMeeting->count();
        }

        protected function meetingChangeScenario($newType, $oldType, &$item)
        {
            $vidPresence = ($item->presence_status == 'P') ? 'AE' : $item->presence_status;
            if ($oldType == 3 && $newType == 2) {
                $item->update(['presence_status' => ($item->presence_status == 'P' || $item->video_presence_status == 'P') ? 'P' : $item->presence_status, 'with_meal_status' => 0, 'video_presence_status' => $vidPresence]);
            } elseif ($oldType == 3 && $newType == 1) {
                $item->update(['video_presence_status' => 'AE', 'register_status' => ($item->presence_status == 'AE') ? 'E' : $item->register_status]);
            } elseif ($oldType == 2 && $newType == 1) {
                $item->update(['video_presence_status' => $item->presence_status, 'with_meal_status' => 0]);

            } elseif ($oldType == 2 && $newType == 3) {

                $item->update(['video_presence_status' => $item->presence_status, 'presence_status' => $vidPresence, 'with_meal_status' => 0]);
            } elseif ($oldType == 1 && $newType == 3) {
                $item->update(['video_presence_status' => $vidPresence]);
            } elseif ($oldType == 1 && $newType == 2) {
                $item->update(['video_presence_status' => $item->presence_status, 'with_meal_status' => 0]);

            }
        }

        protected function changeMeetingStatus($meetingId)
        {
            return Meeting::where('id', $meetingId)->update(['with_meal' => 2]);
        }
        
        public function prepareRequestForBJCreate($request, $meeting_data) {
            $item = [
                'name'              => $meeting_data->name,
                'description'       => $meeting_data->description,
                'start_time'        => $meeting_data->start_time,
                'date'              => $meeting_data->date,
                'end_time'          => $meeting_data->end_time,
                'meeting_date_type' => $meeting_data->meeting_date_type,
            ];
            $reqForBJ = new Request((array)$item);
            $reqForBJ->merge([
                's_time' => $request->start_time,
                'e_time' => $request->end_time,
                'date'   => $request->date,
            ]);
            return $reqForBJ;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description To update the vote status of the workshop admins for the doodle meeting
         * as by default the sec/dep users in workshop will have true vote for all doodle votes from requirements
         * this method will get all workshop admin ids
         * then get the doodle votes for each doodle
         * now this will check for which doodle number of votes != number of w Admins and for them create entry
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param Meeting $meeting
         */
        public function markAdminVotesForDoodles(Meeting $meeting)
        {
            // getting all the workshop admins meta
            $wAdmins = $this->getWorkshopAdminMeta($meeting->workshop_id)->pluck('user_id');
            // getting the ids of doodle dates for meeting
            $doodles = $this->getDoodlesWithAdminVoteOnly($wAdmins, $meeting);
            // the result to insert in db
            $votesToInsert = $this->findMissingDoodleVotesByAdmin($doodles, $wAdmins);
            
            App\DoodleVote::insert($votesToInsert);
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to get the doodles with the votes done by the admin only
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $wAdmins
         * @param $meeting
         * @return Collection
         */
        public function getDoodlesWithAdminVoteOnly($wAdmins, $meeting)
        {
            return DoodleDates::with([
                // as we need only that doodles votes which are from admin
                // so we can find which votes are missing for admin
                'doodleVotes' => function ($q) use ($wAdmins) {
                    $q->whereIn('user_id', $wAdmins);
                }
            ])->where('meeting_id', $meeting->id)->get();
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to find the doodle with admin ids for which doodle don't have voted by the admin
         * the doodle and admin id will be calculated to identify which admin user has not voted for the doodle
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $doodles
         * @param $wAdmins
         * @return array
         */
        public function findMissingDoodleVotesByAdmin($doodles, $wAdmins)
        {
            $votesToInsert = [];
            foreach ($doodles as $doodle) {
                // check if any workshop admin have doodle vote or not
                if(!empty($missingUsers = array_diff($wAdmins->toArray(), $doodle->doodleVotes->pluck('user_id')->toArray()))) {
                    // one or more admin found not voted for this doodle
                    $votesToInsert = array_merge($votesToInsert, $this->prepareDoodleVoteRowForUsers($doodle, $missingUsers));
                }
            }
            return $votesToInsert;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to prepare the db row for each user for the provided doodle
         * @note the vote will be set to 1 i.e. available
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $doodle
         * @param $unVotedUsers
         * @return array
         */
        public function prepareDoodleVoteRowForUsers($doodle, $unVotedUsers)
        {
            $result=  [];
            foreach ($unVotedUsers as $unVUser) {
                $result[] =
                    [
                        'user_id' => $unVUser,
                        'meeting_id' => $doodle->meeting_id,
                        'doodle_id' => $doodle->id,
                        'available' => 1,
                        'updated_id' => NULL
                    ];
            }
            return $result;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to get the workshop meta objects of workshop admins by workshop id
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $workshopId
         * @return Collection<WorkshopMeta>
         */
        public function getWorkshopAdminMeta($workshopId)
        {
            return WorkshopMeta::where('workshop_id', $workshopId)
                // the role must be admin
                ->whereIn('role', [1,2])
                // for safe check user exists in system or not
                ->whereHas('user')
                ->get();
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @descripiton to fetch the upcoming three single date meetings which are nearest in workshop
         * the meeting will be fetched with limited number for repd purpose
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $excludeMId
         * @param $wid
         * @param null $limit // will be fetched from config if not provided
         * @return mixed
         */
        public function getUpcomingMeetingForRepd($excludeMId, $wid, $limit=null) {
            $limit = $limit ? $limit : config('constants.repd_upcoming_limit') ;
            return Meeting::where('workshop_id', $wid)
                // to exclude the current meeting
                ->where('id', '!=', $excludeMId)
                // meeting must be future
                ->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
                // only include the single date meeting
                ->where('meeting_date_type', 1)
                ->where('status', 1)
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get()
                ->take($limit);
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description To find if provided user is workshop admin or not
         * first user will be checked if user has ability to be a sec or not then it will be checked from workshop meta
         * @warn even the user is org or super admin this method will only check if user is workshop sec/dep
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $wid
         * @param $user
         * @return bool
         */
        public function isUserWorkshopAdmin($wid, $user)
        {
            return $user->role == "M2" &&  WorkshopMeta::where('workshop_id', $wid)
                ->where('user_id', $user->id)
                // user must be role 1 or 2
                ->whereIn('role', [1,2])
                ->count();
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to find if user is at least workshop admin (sec or dep) or higher than this role
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $wid
         * @param $user
         * @return bool
         */
        public function isUserWAdminOrHigher($wid, $user) {
            return $user // user must exists
                && ( // if user exists, user should be org or super or w admin
                    $user->role == "M1"  // user is org admin
                    || $user->role == "M0" // or user is super admin
                    || $this->isUserWorkshopAdmin($wid, $user) // or user is workshop member with having admin role
                );
        }
    }


    /*
    
    
    
    
    
     */