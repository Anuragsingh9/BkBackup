<?php


namespace Modules\KctAdmin\Traits;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Services\BusinessServices\factory\ZoomMeetingContentService;
use Modules\KctAdmin\Services\BusinessServices\factory\ZoomWebinarContentService;
use Ramsey\Uuid\Uuid;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the reusable helper functions globally in Super Admin Module
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Trait KctHelper
 * @package Modules\SuperAdmin\Services
 */
trait KctHelper {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the Internal Server Error Exception and return the Json Response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Exception $exception
     * @return JsonResponse
     */
    public function handleIse(Exception $exception): JsonResponse {
        return response()->json([
            'status' => false,
            'msg'    => $exception->getMessage(),
            'trace'  => $exception->getTrace(),
        ], 500);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the 422 response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $message
     * @param array $errors
     * @return JsonResponse
     */
    public function send422($message, $errors = []): JsonResponse {
        return response()->json(['status' => false, 'message' => $message, 'errors' => $errors], 422);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the 404 response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $message
     * @param array $errors
     * @return JsonResponse
     */
    public function send404($message, $errors = []): JsonResponse {
        return response()->json(['status' => false, 'message' => $message, 'errors' => $errors], 404);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the graphics setting all keys
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param bool $byGroup
     * @return array
     */
    public function getGraphicKeys(bool $byGroup = false): array {
        $groupSettings = config('kctadmin.default.group_settings');
        $result = [];
        if ($byGroup) {
            foreach ($groupSettings as $setting => $keys) {
                $result[$setting] = array_keys($keys);
            }
        } else {
            foreach ($groupSettings as $setting => $keys) {
                $result = [...$result, ...array_keys($keys)];
            }
        }
        return $result;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the graphics setting key type/section e.g. colors, checkboxes, etc.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @return int|string|null
     */
    public function findSettingSection($key) {
        $groupSettings = config('kctadmin.default.group_settings');
        foreach ($groupSettings as $section => $keys) {
            if (in_array($key, array_keys($keys))) {
                return $section;
            }
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To convert the date and time to carbon object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $date
     * @param string|null $time
     * @return Carbon|false
     */
    public function getCarbonByDateTime(string $date, ?string $time = null) {
        return Carbon::createFromFormat(
            config('kctadmin.constants.dateFormat'),
            $time ? "$date $time" : $date
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will check
     * - if the key is present in $check
     *      then in $target key update value from $source->key
     *      return target with updated key from $check
     * - else
     *      do nothing with target and return target as it came
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $key // key to check
     * @param array $target // array of which value will be update if source have that key
     * @param array $source // array from which check if key is present in or not
     * @return array
     */
    public function insertIfIsset(string $key, array $target, array $source): array {
        if (isset($source[$key])) {
            $target[$key] = $source[$key];
        }
        return $target;
    }

    /**
     * @param $users
     * @return array
     */
    public function collectId($users): array {
        $userId = [];
        foreach ($users as $user) {
            $userId[] = $user['id'];
        }
        return $userId;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for converting rgba color code into hex color code
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $rgba
     * @return string
     */
    public function rgbaToHex($rgba): string {
        $hex = sprintf("#%02x%02x%02x", $rgba['r'], $rgba['g'], $rgba['b']);
        $alphaDec = $rgba['a'] * 255;
        $alpha = dechex($alphaDec);
        return $hex . $alpha;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for converting hex color code into rgba color code
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $hex
     * @return array
     */
    public function hexToRgba($hex): array {
        $hex = strtoupper($hex);
        $hexValue = str_replace('#', '', $hex);
        $length = strlen($hexValue);
        if ($length == 6) {
            $hexValue .= "FF";
        } else if ($length == 3) {
            $hexValue = "$hexValue[0]$hexValue[0]$hexValue[1]$hexValue[1]$hexValue[2]$hexValue[2]FF";
        } else if ($length < 8) {
            $hexValue .= str_repeat("F", 8 - $length);
        }
        $alpha = round(hexdec("$hexValue[6]$hexValue[7]") / 255, 2);
        return [
            'r' => hexdec("$hexValue[0]$hexValue[1]"),
            'g' => hexdec("$hexValue[2]$hexValue[3]"),
            'b' => hexdec("$hexValue[4]$hexValue[5]"),
            'a' => $alpha,
        ];

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will take input string and will check if the request attribute string is coming inside
     * array or directly
     *
     * e.g. users.0.fname -> there is attribute is fname, but it is coming in array so only attribute should be return
     * e.g. fname -> there is directly attribute coming, so no index value prepared
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $attribute
     * @return array
     */
    public function setAttribute($attribute): array {
        $exp = explode('.', $attribute);
        $atr = Arr::last($exp);
        if (count($exp) == 3) { // inside single array
            return [
                'key'       => $exp[0] ?? null,
                'index'     => $exp[1] ?? null,
                'attribute' => $atr,
            ];
        } else {
            return [
                'key'       => $exp[0] ?? null,
                'index'     => null,
                'attribute' => $atr,
            ];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the keys belongs to zoom or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @return bool
     */
    public function isZoomKey($key): bool {
        return in_array($key, $this->getZoomKeys());
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the zoom related technical setting keys
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string[]
     */
    public function getZoomKeys(): array {
        return [
            'custom_zoom_settings', 'default_zoom_settings'
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the service class according to setting key updating
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $field
     * @return mixed
     */
    public function getZoomService($field) {
        $service = $field == 'zoom_meeting_settings'
            ? $this->adminServices()->zoomMeetingService
            : $this->adminServices()->zoomWebinarService;

        if ($field == 'zoom_default_webinar_settings') {
            $service->settingKey = 'zoom_default_webinar_settings';
        }

        return $service;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if event is future event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return bool
     */
    public function isFutureEvent($event): bool {
        $start_time = $this->getCarbonByDateTime($event->start_time)->timestamp;
        $current_time = Carbon::now()->timestamp;
        return $start_time > $current_time;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if event is past event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return bool
     */
    public function isPastEvent($event): bool {
        $start_time = $this->getCarbonByDateTime($event->start_time)->timestamp;
        $end_time = $this->getCarbonByDateTime($event->end_time)->timestamp;
        $current_time = Carbon::now()->timestamp;
        return $start_time < $current_time && $end_time < $current_time;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if event is live event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return bool
     */
    public function isLiveEvent($event): bool {
        $start_time = $this->getCarbonByDateTime($event->start_time)->timestamp;
        $end_time = $this->getCarbonByDateTime($event->end_time)->timestamp;
        $current_time = Carbon::now()->timestamp;
        return $current_time >= $start_time && $end_time >= $current_time;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the moment type by setting key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     */
    public function getMomentTypeByKey($key, $throw = true) {
        $keys = [
            'default_zoom_settings' => Moment::$momentType_webinar,
            'zoom_meeting_settings' => Moment::$momentType_meeting,
        ];
        if ($throw && !isset($keys[$key])) {
            throw new Exception("Undefined Broadcast");
        }
        return $keys[$key] ?? null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the moment type by setting key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $type
     * @return string
     * @throws Exception
     */
    public function getMomentKeyByType($type) {
        $keys = [
            Moment::$momentType_defaultWebinar => 'zoom_default_webinar_settings',
            Moment::$momentType_webinar        => 'zoom_webinar_settings',
            Moment::$momentType_meeting        => 'zoom_meeting_settings',
        ];
        if (!isset($keys[$type])) {
            throw new Exception("Undefined Broadcast");
        }
        return $keys[$type];
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if moment is broadcast type or not;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $type
     * @return ZoomMeetingContentService|ZoomWebinarContentService|null
     */
    public function getBroadcastClassByMomentType($type) {
        if ($type == Moment::$momentType_defaultWebinar) {
            $this->adminServices()->zoomWebinarService->settingKey = 'zoom_default_webinar_settings';
            return $this->adminServices()->zoomWebinarService;
        } else if ($type == Moment::$momentType_webinar) {
            return $this->adminServices()->zoomWebinarService;
        } else if ($type == Moment::$momentType_meeting) {
            return $this->adminServices()->zoomMeetingService;
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the space hots in event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return array
     */
    public function getSpaceHosts($event): array {
        $collection = new Collection();
        $spaceList = $event->spaces;
        foreach ($spaceList as $space) {
            $collection[] = $space->hosts[0]['id'];
        }
        return array_unique($collection->toArray());
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find biggest dimension among width and height and the resize image's biggest dimension to 48px.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $icon
     * @return \Intervention\Image\Image
     */
    public function resizeIcon($icon): \Intervention\Image\Image {
        $image = Image::make($icon);
        $height = $image->height();
        $width = $image->width();
        $maxDimension = $width > $height ? 'width' : 'height';
        if ($maxDimension == 'width') {
            $newWidth = 48;
            $newHeight = null;
        } else {
            $newWidth = null;
            $newHeight = 48;
        }
        $image->resize($newWidth, $newHeight, function ($constraint) {
            $constraint->aspectRatio();
        });
        return $image;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will return all the admin roles in an array
     *
     * e.g:- [2,3,4] // 2 = main pilot, 3 = owner, 4 = co-pilot
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function getAdminRoles(): array {
        return [
            GroupUser::$role_Organiser, GroupUser::$role_owner, GroupUser::$role_co_pilot
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will generate and return universally unique identifier.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string
     */
    public function generateUuid(): string {
        return Uuid::uuid4()->toString();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will check that if image dimension is greater than given dimensions as parameters
     *  then it will resize the image to the given dimension and return intervention image instance.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $file
     * @param $maxWidth
     * @param $maxHeight
     * @return \Intervention\Image\Image
     */
    public function resizeImage($file, $maxWidth, $maxHeight): \Intervention\Image\Image {
        $image = Image::make($file);
        $imgHeight = $image->height();
        $imgWidth = $image->width();
        if ($imgHeight > $maxHeight || $imgWidth > $maxWidth) {
            return $image->resize($maxWidth, $maxHeight);
        }
        return $image;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the video id by the url(YouTube)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ?string $url
     * @return ?string
     */
    public function getYoutubeIdByUrl($url): ?string {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1] ?? null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the video id by the url(Vimeo)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $url
     * @return string|null
     */
    public function getVimeoIdByUrl($url): ?string {
        preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $url, $match);
        return Arr::last($match);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the thumbnail link by the video link
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $type
     * @param $videoId
     * @return string|null
     */
    public function getVideoThumbnailUrl($type, $videoId): ?string {
        if ($type == 1) {
            // youtube
            return "http://img.youtube.com/vi/$videoId/mqdefault.jpg";
        } else if ($type == 2) {
            return "https://vumbnail.com/$videoId.jpg";
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method generate random string with required length
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $length
     * @param bool $numeric
     * @return string
     */
    function generateRandomString($length = 10, $numeric = true): string {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($numeric) {
            $characters .= '0123456789';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will return the event's date and time as per the data requested.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $type
     * @return string|null
     */
    public function getEventDateTime($event, $type): ?string {
        $event->load('eventRecurrenceData');
        switch ($type) {
            case 'start_time':
                return $this->getCarbonByDateTime($event->start_time)->toTimeString();
            case 'end_time':
                return $this->getCarbonByDateTime($event->end_time)->toTimeString();
            case 'start_date':
                return $this->getCarbonByDateTime($event->start_time)->toDateString();
            case 'end_date':
                return $this->getCarbonByDateTime($event->end_time)->toDateString();
            case 'recurring_end_date':
                return isset($event->eventRecurrenceData) ? $event->eventRecurrenceData->end_date : null;
            case 'recurring_start_date':
                return isset($event->eventRecurrenceData) ? $event->eventRecurrenceData->start_date : null;
            default:
                return null;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will extract and return the subdomain from account name.
     * @return mixed|string
     * @example
     * account name = first.humannconnect.com
     * subdomain = first
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    public function getSubDomainFromAccount() {
        $account = request()->getHost();
        $account = explode('.', $account);
        return $account[0];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To return the resource with having the pre-set additional values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $resource
     * @param array $additional
     * @return mixed
     */
    public function returnWithStatus($resource, $additional = []) {
        $additional = array_merge($additional, ['status' => true]);
        return $resource->additional($additional);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will simply check if query data should be paginated or not and according to the
     * parameters it will return either paginated or simple data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $queryData
     * @param $isPaginated
     * @param $pagePerSize
     * @return mixed
     */
    public function handleDataPagination($queryData, $isPaginated, $pagePerSize) {
        if ($isPaginated) {
            return $queryData->paginate($pagePerSize);
        } else {
            return $queryData->get();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for removing given user roles from an user. The roles will be removed
     * from model_has_role table.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $roles
     * @param $user
     */
    public function removeRoles($roles, $user) {
        foreach ($roles as $role) {
            $user->removeRole($role);
        }
    }

    public function isTimeValid($target, $format) {
        try {
            if ($carbon = Carbon::createFromFormat($format, $target)) {
                return $carbon;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method returns the user grade
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $user
     * @return string
     */
    public function getUserGradeRole($user): string {
        if ($user->hasRole('executive')) {
            $grade = 'executive';
        } elseif ($user->hasRole('manager')) {
            $grade = 'manager';
        } elseif ($user->hasRole('employee')) {
            $grade = 'employee';
        } elseif ($user->hasRole('other')) {
            $grade = 'other';
        } else {
            $grade = 'employee';
        }
        return $grade;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @@descripiton This method is used for prepare the formatted data for attendance
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return array
     */
    public function attendanceDataFormat($event): array {
        $userData = [];
        foreach ($this->resource->userJoinReports as $attendance) {
            $userData[] = [
                'time'    => $attendance['created_at']->format('Y-m-d H:i:s'),
                'action'  => 'login',
                'user_id' => $attendance['user_id'],
                'grade'   => $this->getUserGradeRole($attendance->user),
            ];
            $endTime = $attendance['on_leave'] ?? $event->end_time;
            $userData[] = [
                'time'    => $this->getCarbonByDateTime($endTime)->format('Y-m-d H:i:s'),
                'action'  => 'logout',
                'user_id' => $attendance['user_id'],
                'grade'   => $this->getUserGradeRole($attendance->user),
            ];
        }
        return $userData;
    }
}
