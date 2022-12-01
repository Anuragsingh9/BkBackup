<?php


namespace Modules\KctAdmin\Services\BusinessServices\factory;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\Label;
use Modules\KctAdmin\Entities\LabelLocale;
use Modules\KctAdmin\Services\BusinessServices\IGroupService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\V1\GroupSettingArrayResource;
use Ramsey\Uuid\Uuid;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the group management related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupService
 * @package Modules\KctAdmin\Services\BusinessServices\factory
 */
class GroupService implements IGroupService {
    use ServicesAndRepo;
    use KctHelper;
    use \Modules\UserManagement\Traits\ServicesAndRepo;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the default settings for the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param ?int $isDefault
     * @throws Exception
     */
    private function setDefaultGrpSetting(int $groupId, ?int $isDefault) {
        $this->syncGroupSettings($groupId);
        $this->copyDefaultLogo($groupId);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used to copy default logo
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     */
    public function copyDefaultLogo($groupId) {
        $extension = pathinfo(config('superadmin.default_logo'), PATHINFO_EXTENSION);
        $fqdn = $this->umServices()->tenantService->getFqdn();
        $newPath = "$fqdn/" . config('kctadmin.constants.storage_paths.group_logo')
            . "/" . Uuid::uuid1()->toString() . ".$extension";
        $this->adminServices()->fileService->copyFile(
            config('superadmin.default_logo'),
            $newPath
        );
        $this->adminRepo()->settingRepository->setSetting(
            $groupId,
            config('kctadmin.constants.setting_keys.group_logo'),
            [config('kctadmin.constants.setting_keys.group_logo') => $newPath]
        );
    }

    /**
     * @inheritDoc
     */
    public function createGroup(array $data, $groupType) {
        $group = $this->adminRepo()->groupRepository->createGroup($data, $groupType);
        $this->setDefaultGrpSetting($group->id, $group->is_default);
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function setGroupLogo(int $groupId, ?UploadedFile $logo = null) {
        if (!$logo) {
            $logo = ['url' => ''];
        }
        $settingKeys = config('kctadmin.constants.setting_keys');
        return $this->adminRepo()->settingRepository->setSetting(
            $groupId,
            config('kctadmin.constants.setting_keys.group_logo'),
            $logo
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setGroupSettings(int $groupId, array $settings) {
        foreach ($settings as $key => $value) {
            $setting = $this->adminRepo()->settingRepository->getSettingByKey($key);

            if (!$setting) { // setting not present in db, must be first time for setting up value
                if (!isset($value)) {
                    // data is not proper for the first time
                    throw new Exception("Data not proper for $key");
                }
                $dataToInsert = [];
            } else {
                $dataToInsert = $setting->setting_value;
            }
            $dataToInsert = $this->insertIfIsset($key, $dataToInsert, $settings);
            $data[] = $this->adminRepo()->settingRepository
                ->setSetting($groupId, $key, $dataToInsert);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function syncGroupSettings(int $groupId): int {
        $keys = $this->getGraphicKeys();
        $settings = $this->adminRepo()->settingRepository->getSettingsByKey($groupId, $keys);
        $count = 0;
        if (count($keys) > $settings->count()) {
            $keysInDB = $settings->pluck('setting_key')->toArray();
            $missingKeys = array_diff($keys, $keysInDB);
            foreach ($missingKeys as $missingKey) {
                $keyType = $this->findSettingSection($missingKey);
                $this->adminRepo()->settingRepository->setSetting(
                    $groupId,
                    $missingKey,
                    $keyType == 'arrays'
                        // for arrays directly store the array as it is
                        ? config("kctadmin.default.group_settings.$keyType.$missingKey")
                        : [
                        $missingKey => config("kctadmin.default.group_settings.$keyType.$missingKey"),
                    ],
                );
                $count++;
            }
        }
        $this->syncEventDefaultImage($groupId);
        $this->syncGroupLogo($groupId);
        return $count;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used for synchronize the event default image in the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     */
    public function syncEventDefaultImage($groupId) {
        $group = $this->adminRepo()->groupRepository->findGroupById($groupId);
        $setting = $group->setting()->where('setting_key', 'event_image')->first();
        if ($setting && $setting->setting_value && $setting->setting_value['event_image'] == null) {
            $value = $setting->setting_value;
            $value['event_image'] = config('kctadmin.constants.event_default_image_path');
            $setting->setting_value = $value;
            $setting->update();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used for synchronize the group logo in the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     */
    public function syncGroupLogo($groupId) {
        $group = $this->adminRepo()->groupRepository->findGroupById($groupId);
        $setting = $group->setting()->where('setting_key', 'video_explainer_alternative_image')->first();
        if ($setting && $setting->setting_value && $setting->setting_value['video_explainer_alternative_image'] == null) {
            $value = $setting->setting_value;
            $imagePath = $this->adminServices()->superAdminService->getUserGridImage();
            $value['video_explainer_alternative_image'] = $imagePath;
            $setting->setting_value = $value;
            $setting->update();
        }
    }

    /**
     * @inheritDoc
     */
    public function prepareGroupKey(Group $group) {
        $specialChar = false;
        $groupName = $group->name;
        if (str_word_count($groupName) == 1) { // Entered group name is a single word
            $name = substr($groupName, 0, 2);
            // First two characters of the group name are not alphabets
            if ($this->containSpecialChar($name)) {
                $subDomain = $this->getSubDomainFromAccount();
                $name = substr($subDomain, 0, 3);
                $specialChar = true;
            }
        } else {
            $firstChar = substr($groupName, 0, 1);
            $secondWord = explode(' ', $groupName);
            if (isset($secondWord[1])) { // Entered group name have more than one words
                // Checking if the first word or second word contains any special characters
                if ($this->containSpecialChar($firstChar) || $this->containSpecialChar($secondWord[1]) ) {
                    $subDomain = $this->getSubDomainFromAccount();
                    $name = substr($subDomain, 0, 3);
                    $specialChar = true;
                } else {
                    $secondChar = substr($secondWord[1], 0, 1);
                    $name = $firstChar . $secondChar;
                }
            } else {
                // Entered group name have two words and starting character of second word contains special
                // characters without space
                $subDomain = $this->getSubDomainFromAccount();
                $name = $secondWord[1] ?? substr($subDomain, 0, 3);
                $specialChar = true;
            }
        }
        $num = $specialChar
            ? sprintf("%03d", $group->toArray()['id'])
            : sprintf("%04d", $group->toArray()['id']);
        return strtoupper($name) . $num;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if the given word contains any special characters or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $word
     * @return bool
     */
    public function containSpecialChar($word): bool {
        return !preg_match("/^[a-zàâäáçéèêëìîïíôóòûùüúÿñæœÀÂÁÇÉÈÊËÎÏÌÍÔÛÙÜÚŸÑÆŒ¿ ̛̔̕'’-]*$/i", $word);
    }

    /**
     * @inheritDoc
     */
    public function syncBroadcastingSettings(int $groupId, ?Collection $keys = null) {
        $keys = $keys ?: $this->adminRepo()->settingRepository->getSettingsByKey(
            $groupId,
            $this->getZoomKeys()
        );

        $existingKeys = $keys->pluck('setting_key')->toArray();
        $missingKeys = array_diff($this->getZoomKeys(), $existingKeys);

        foreach ($missingKeys as $missingKey) {
            $this->adminRepo()->settingRepository->setSetting(
                $groupId,
                $missingKey,
                config("kctadmin.broadcast_keys.$missingKey")
            );
        }

    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function updateArraySettings(int $groupId, $value, $field): array {
        $existing = $this->adminRepo()->settingRepository->getSettingByKey($field);
        $previous = $existing->setting_value ?? [];
        if (isset($value['is_assigned']) && !$value['is_assigned']) {
            // removing license so setting the things to initial state
            $value = config("kctadmin.default.group_settings.arrays.$field");
            $previous = [];
        }
        if ($this->isZoomKey($field) && isset($value['licenses']) && ($previous['is_assigned'] ?? 0) == 1) {
            $this->updateZoomLicenses($previous['licenses'] ?? [], $value['licenses'], $field);
        }
        $data = array_merge($previous, $value);
        $value = $data;
        $existing->setting_value = $value;
        $returnValue = new GroupSettingArrayResource($existing);
        return [
            'returnValue' => $returnValue,
            'value'       => $value
        ];
    }

    /**
     * @throws Exception
     * @deprecated
     */
    public function updateZoomLicenses($previousLicenses, $newLicenses, $field): array {
        $usersRemoved = array_values(array_diff($previousLicenses, $newLicenses));
        $usersAdded = array_values(array_diff($newLicenses, $previousLicenses));

        $usersRemoved = count($usersRemoved) ? $this->adminServices()->userService->getUsersById($usersRemoved) : [];
        $usersAdded = count($usersAdded) ? $this->adminServices()->userService->getUsersById($usersAdded) : [];

        $service = $this->getZoomService($field);

        foreach ($usersRemoved as $removeUser) {
            $service->removeUserLicense($removeUser);
        }

        $newLicenses = [];

        foreach ($usersAdded as $addedUser) {
            if ($service->addUserLicense($addedUser)) {
                $newLicenses[] = $addedUser->id;
            }
        }
        return $newLicenses;
    }

    /**
     * @inheritDoc
     */
    public function fetchTechnicalSettings($groupId) {
        $technicalSettings = $this->adminRepo()->settingRepository->getSettingsByKey(
            $groupId,
            $this->getZoomKeys()
        );
        if ($technicalSettings->count() != count($this->getZoomKeys())) {
            $this->adminServices()->groupService
                ->syncBroadcastingSettings($groupId, $technicalSettings);
            $technicalSettings = $this->adminRepo()->settingRepository->getSettingsByKey(
                $groupId,
                $this->getZoomKeys()
            );
        }

        $technicalSettings->map(function ($technicalSetting) {
            // checking hosts are fetched or not for the technical setting
            if (isset($technicalSetting->setting_value['webinar_data']['hosts']) && !empty($technicalSetting->setting_value['webinar_data'])) {
                $technicalSetting = $this->adminServices()->zoomService->fetchHost($technicalSetting, 'webinar_data');
                $technicalSetting = $this->adminServices()->zoomService->fetchHost($technicalSetting, 'meeting_data');
            }
            return $technicalSetting;
        });
        return $technicalSettings;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getSuperGroup($accountId) {
        return $this->adminRepo()->groupRepository->getDefaultGroup();
    }

    /**
     * @inheritDoc
     */
    public function getUserCurrentGroup($userId) {
        $currentGroup = $this->adminRepo()->groupUserRepository->getUserCurrentGroupId($userId);
        if ($currentGroup && $currentGroup->last_visit) {
            $currentGroupId = $currentGroup->group_id;
        } else {
            $currentGroupId = $this->adminRepo()->groupUserRepository->getUserFirstAddedGroupId($userId);
        }
        return $this->adminRepo()->groupRepository->findById($currentGroupId);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentUserGroups($userId) {
        return $this->adminRepo()->groupUserRepository->getCurrentUserGroups($userId);
    }

    /**
     * @inheritDoc
     */
    public function isUserGroupAdmin(?Group $group, int $userId) {
        return $group->groupUser()->where('user_id', $userId)
            ->whereIn('role', [2, 3, 4])->first();
    }

    /**
     * @inheritDoc
     */
    public function copyGroupTags($from, $to) {
        $param = [];
        $superGrpTags = $this->adminRepo()->orgTagsRepository->getByGroupId($from, 'name', 'asc');
        foreach ($superGrpTags as $tag) {
            $param[] = [
                'tag_id'     => $tag->id,
                'group_id'   => $to,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        $this->adminRepo()->orgTagsRepository->storeMultipleTags($param);
    }

    /**
     * @inheritDoc
     */
    public function isSuperPilotOrOwner() {
        return $this->adminRepo()->groupRepository->isSuperPilotOrOwner();
    }

    /**
     * @inheritDoc
     */
    public function syncLabels(Group $group) {
        $labels = [
            'space_host'    => 'Space Host',
            'business_team' => 'Team A',
            'expert'        => 'Team B',
            'vip'           => "Vip",
            'moderator'     => "Moderator",
            'speaker'       => "Speaker",
            'participants'  => "Participants",
            'owners'        => "Owners",
            'pilots'        => "Pilots"
        ];
        $availableLang = array_keys(config("kctadmin.moduleLanguages"));
        foreach ($labels as $label => $value) {
            $label = Label::firstOrCreate([
                'name' => $label,
            ]);

            foreach ($availableLang as $lang) {
                LabelLocale::firstOrCreate([
                    'label_id' => $label->id,
                    'locale'   => $lang,
                    'group_id' => $group->id,
                ], [
                    'value' => $value,
                ]);
            }
        }
    }

}
