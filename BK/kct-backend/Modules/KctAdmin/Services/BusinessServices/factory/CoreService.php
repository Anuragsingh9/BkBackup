<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;


use Carbon\Carbon;
use Exception;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Entities\Space;
use Modules\KctAdmin\Exceptions\ZoomGrantException;
use Modules\KctAdmin\Services\BusinessServices\ICoreService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Ramsey\Uuid\Uuid;

class CoreService implements ICoreService {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * @inheritDoc
     */
    public function setDefaultLogoUrl($path) {
        $setting = $this->settingRepository->getSettingByKey("event_settings");
        if ($setting) {
            // event setting already present

            // deleting the previous logo if not universal logo
            $this->deleteKctDefaultLogo($setting);

            // setting new logo to database setting table
            $this->setKCTSettingValue('kct_graphics_logo', $path, $setting);
        }
    }

    /**
     * @inheritDoc
     */
    public function setKCTSettingValue($key, $value, $setting = null) {
        if (!$setting) {
            $setting = $this->settingRepository->getSettingByKey("event_settings");
        }
        if ($setting) {
            $data = $setting->setting_value;
            $data['event_kct_setting'][$key] = $value;
            $this->settingRepository->updateSettingValuebySettingKey(json_encode($data), "event_Settings");
            $setting = $this->settingRepository->getSettingByKey("event_settings");
            return $setting->setting_value['event_kct_setting'][$key];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getCustomGraphicsSetting(): array {
        $settingAfterDecode = $this->getDecodeSetting(config('kctadmin.setting_keys.event_custom_graphics'));

        if (!$settingAfterDecode) {
            $this->setDefaultCustomGraphics();
            $settingAfterDecode = $this->getDecodeSetting(config('kctadmin.setting_keys.event_custom_graphics'));
        }
        // todo verify each key if new introduced

        // $settingAfterDecode = $this->validateCustomizationKeys($settingAfterDecode);
        return $settingAfterDecode;
    }

    /**
     * @inheritDoc
     */
    public function updateCustomGraphics($field, $value) {
        // $setting = GroupSetting::where(['setting_key' => config('kctadmin.setting_keys.event_custom_graphics')])
        $setting = $this->settingRepository->getSettingByKey(config('kctadmin.setting_keys.event_custom_graphics'));
        if (!$setting) {
            $this->setDefaultCustomGraphics();
//             $setting = GroupSetting::where(['setting_key' => config('kctadmin.setting_keys.event_custom_graphics')])
//                 ->first();
            $setting = $this->settingRepository->getSettingByKey(config('kctadmin.setting_keys.event_custom_graphics'));
        }
//        $prev = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
        $prev = $this->prepareCustomizationResource($setting);
        if ($prev) {
            $fieldType = $this->findFieldType($field);
            if ($fieldType == 'color') {
                // this will ensure only rgba is save in case if value contains extra keys
                $value = json_decode($value, JSON_OBJECT_AS_ARRAY);
                $update = [
                    'r' => $value['r'],
                    'g' => $value['g'],
                    'b' => $value['b'],
                    'a' => $value['a'],
                ];
            } else if ($fieldType == 'checkbox') {
                $update = (int)$value;
            } else if ($fieldType == 'number') {
                $update = (int)$value;
            } else if ($fieldType == 'label') {
                $update = json_decode($value, JSON_OBJECT_AS_ARRAY);
            } else {
                $update = $value;
            }
            $prev[$field] = $update;
            // GroupSetting::where(['setting_key' => config('kctadmin.setting_keys.event_custom_graphics')])
            //     ->update([
            //         'setting_value' => json_encode($prev)
            //     ]);
            $this->settingRepository->updateSettingValuebySettingKey(json_encode($prev), config('kctadmin.setting_keys.event_custom_graphics'));
        }
    }

    /**
     * @inheritDoc
     */
    public function setDefaultLogo() {
        $path = $this->getDefaultLogoForKct();
        $this->setDefaultLogoUrl($path);
        $this->getBaseService()->fileService->getFileUrl($path);
    }

    /**
     * @inheritDoc
     */
    public function createSpace($param): Space {
        $space = $this->getBaseService()->spaceService->create($param);
        if (isset($param['hosts']) && $param['hosts']) {
            $this->addSpaceHosts($param['hosts'], $space);
        }
        $space->load('hosts');
        return $space;
    }

    /**
     * @inheritDoc
     */
    public function prepareCustomizationResource($setting): array {

        $defaults = config('kctadmin.default.custom_graphics');

        // this will check the value is present in setting or not in case not it will return the default value
        $customIsset = function ($value, $valueIfNotFound) use ($setting) {
            return isset($setting->$value) ? $setting->$value : $valueIfNotFound;
        };

        $data = [];

        foreach ($defaults as $keysArray) {
            // now in keys array there will be specific type of keys like, color, checkboxes keys etc.
            foreach ($keysArray as $key => $defaultValue) {
                $data[$key] = $customIsset($key, $defaultValue);
            }
        }

        return $data;
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To prepare all types of links like moderator,speaker,attendee and manual access.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @throws ZoomGrantException
     */
    public function prepareAccessLinks(Event $event, bool $returnAllLinks = false): array {
        $mainDomain = env("APP_FRONT_HOST");
        $subDomain = $this->getSubDomain(request());
        $participantsLinks = $this->prepareParticipantsLink($event);
        if (!isset($event->event_settings['manual_access_code'])) {
            $setting = $event->event_settings;
            $setting['manual_access_code'] = Uuid::uuid4();
            $event->event_settings = $setting;
            $event->update();
        }
        $manualCode = $event->event_settings['manual_access_code'];
        $links = [
            'participants_link' => $participantsLinks,
        ];
        if ($event->event_type != Event::$eventType_all_day) {
            $links['manual_access'] = env("HOST_TYPE")
                . "$subDomain.$mainDomain/dashboard/$event->event_uuid?access_code=$manualCode";
        }
        return $this->prepareBroadcastingLinks($event, $returnAllLinks, $links);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will prepare all broadcasting related links
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param bool $returnAllLinks
     * @param array|null $links
     * @return array|null
     * @throws ZoomGrantException|Exception
     */
    public function prepareBroadcastingLinks(Event $event, bool $returnAllLinks = false, ?array $links = []): ?array {
        if ($returnAllLinks) {
            $moderatorMoments = $speakerMoments = $event->moments()->whereIn('moment_type', [2, 3, 4])->get();
        } else {
            $moderatorMoments = $event->moderatorMoments;
            $speakerMoments = $event->speakerMoments;
        }
        if ($moderatorMoments->count()) {
            foreach ($moderatorMoments as $moment) {
                if ($moment->moment_id) {
//                    $links['moderator_links'] = [
//                        'id'          => $moment->id,
//                        'moment_name' => $moment->moment_name,
//                        'link'        => $this->getModeratorLink($moment),
//                    ];
                    $links['moderator_links'] = $this->getModeratorLink($moment);
                }
            }
        }
        if ($speakerMoments->count()) {
            foreach ($speakerMoments as $moment) {
                if ($moment->moment_id) {
//                    $links['speaker_links'] = [
//                        'id'          => $moment->id,
//                        'moment_name' => $moment->moment_name,
//                        'link'        => $moment->moment_settings['join_url'] ?? null,
//                    ];
                    $links['speaker_links'] = $moment->moment_settings['join_url'] ?? null;
                }
            }
        }
        return $links;
    }

    /**
     * @param Moment $moment
     * @return mixed|null
     * @throws ZoomGrantException|Exception
     */
    public function getModeratorLink(Moment $moment) {
        if ($moment->moment_type == 2 || $moment->moment_type == 3) {
            return $moment->moment_settings['moderator_url'] ?? null;
        } else {
            $carbonNow = Carbon::now();
            if ($carbonNow->timestamp < ($moment->moment_settings['start_url_expire'] ?? 0)) {
                return $moment->moment_settings['moderator_url'];
            } else {
                // as moderator url is expired so getting it again
                $meeting = $this->adminServices()->zoomService->getMeeting($moment->moment_id);
                if (isset($meeting['start_url'])) {
                    // new url fetched so storing in db so till next expire we can use it again
                    $momentSetting = $moment->moment_settings;
                    $momentSetting['moderator_url'] = $meeting['start_url'];
                    $momentSetting['start_url_expire'] = Carbon::now()->addHours(2)->timestamp;
                    $moment->moment_settings = $momentSetting;
                    $moment->update();
                    return $moment->moment_settings['moderator_url'];
                }
                return $moment->moment_settings['join_url'] ?? null;
            }
        }
    }


    /**
     * @throws Exception
     */
    public function getMomentEmbeddedUrl(?Moment $moment): ?string {
        $key = $this->getMomentKeyByType($moment->moment_type);
        return $this->adminServices()->zoomService->getEmbeddedUrl($moment);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the link for the participants of the event with the help of join code
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @return string
     */
    public function prepareParticipantsLink(Event $event): string {
        return route('event-join', ['join_code' => $event->join_code]);
    }

    public function getSubDomain($request) {
        $subDomain = explode('.', $request->getHost());
        if (count($subDomain) > 1) {
            $subDomain = $subDomain[0];
        } else {
            $subDomain = '';
        }
        return $subDomain;
    }
}
