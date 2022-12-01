<?php

namespace Modules\SuperAdmin\Database\Seeders;

use App\Models\User;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class S3ImageTransferTableSeeder extends Seeder {
    use ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;

    private ?string $fqdn;
    private Environment $tenant;

    public function __construct(Environment $tenant) {
        $this->tenant = $tenant;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $hostnames = Hostname::all();
        foreach ($hostnames as $hostname) {
            $this->tenant->tenant($hostname->website);
            $this->fqdn = $hostname->fqdn;
            $groups = Group::all();
            foreach ($groups as $group) {
                $this->syncGroupSettingsImages($group);
                $this->syncUsersImages();
            }
        }
    }


    private function syncGroupSettingsImages(Group $group) {
        $settings = array_keys(config('kctadmin.default.group_settings.images'));
        $groupSettings = $group->allSettings()->whereIn('setting_key', $settings)->get();
        foreach ($groupSettings as $groupSetting) {
            $storedPath = $groupSetting->setting_value[$groupSetting->setting_key];
            $this->checkAndMove($storedPath);
        }
    }

    private function checkAndMove($storedPath) {
        if (!$storedPath) {
            return;
        }
        $targetPath = "$this->fqdn/" . str_replace("$this->fqdn/", "" , $storedPath);
        $isStoredExists = $this->adminServices()->fileService->isFileExists($storedPath);
        $isTargetExists = $this->adminServices()->fileService->isFileExists("$targetPath");
        $a = $isStoredExists ? 'exists' : 'not exists';
        $b = $isTargetExists ? 'exists' : 'not exists';
        if (!Str::startsWith($storedPath, ['users', "$this->fqdn/users"])) {
//            printf("----------------------------------------------------------------\n$storedPath\nisStoredFileExists = $a - {$storedPath}\nisTargetFileExists = $b - $targetPath\n");
        }
        if ($isStoredExists && !$isTargetExists && ($storedPath != $targetPath) && !Str::startsWith($storedPath, ['general', 'assets'])) {
            $moveResult = $this->adminServices()->fileService->moveFile($storedPath, "$targetPath");
            printf("moving file $moveResult\n");
        }
        return str_replace("$this->fqdn/", "" , $targetPath);
    }

    private function syncUsersImages() {
        $allUsers = User::whereNotNull('avatar')->get();
        foreach ($allUsers as $user) {
            $user->avatar = $this->checkAndMove($user->avatar);
            $user->update();
        }
    }
}
