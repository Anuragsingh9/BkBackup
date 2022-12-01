<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\SuperAdmin\Entities\User;

class MigrationVersion3Seeder extends Seeder {
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
        $allGroups = Group::all();
        $this->defaultGroup = $this->adminRepo()->groupRepository->getDefaultGroup(false);
        if(!$this->defaultGroup){
            return;
        }
        $this->migrateMissingPilots($allGroups);
        $this->migrateMissingGroupKey($allGroups);
    }

    private function migrateMissingPilots($allGroups) {
        $firstPilot = User::find(1);
        foreach ($allGroups as $group) {
            // checking the existence of any pilot in group
            $pilotExist = GroupUser::whereGroupId($group->id)->whereRole(GroupUser::$role_Organiser)->exists();
            if (!$pilotExist && $firstPilot && $firstPilot->id) {
                /* no pilots found in the group, so adding first user of the organisation(acc created by user)
                as pilot of the group
                */
                $this->adminRepo()->groupRepository->addUser($firstPilot->id, $group->id, GroupUser::$role_Organiser);
            }
        }
    }

    /**
     * @param Collection[Group] $allGroups
     */
    private function migrateMissingGroupKey(Collection $allGroups) {
        foreach ($allGroups as $group) {
            if (!$group->group_key) {
                $group->group_key = $group->id == $this->defaultGroup->id
                    ? 'default'
                    : $this->adminServices()->groupService->prepareGroupKey($group);
                $group->update();
            }
        }
    }
}
