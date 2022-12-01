<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Repositories\factory\GroupRepository;
use Modules\KctAdmin\Services\BusinessServices\factory\GroupService;

class GroupDashboardResource extends JsonResource {


    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    private $grpService;


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'id'               => $this->id,
            'group_name'       => $this->name,
            'group_short_name' => $this->short_name,
            'description'      => $this->description,
            'settings'         => $this->settings,
            'security'         => SecurityResource::collection($this->security),
            'group_organiser'  => GrpOrganiserResource::collection($this->getGroupOrganiser($this->id)), // currently we don't have users model for now,showing this way
            'total_users'      => $this->getUserCount($this->id),
        ];
    }

    protected function getGroupOrganiser($id) {
        $groupOrgId = app(GroupRepository::class)->getGrpOrganiser($id);
        $org = app(GroupService::class)->getUser($groupOrgId);
        return $org;
    }

    protected function getUserCount($id) {
        return $this->grpService = app(GroupRepository::class)->countGroupUsers($id);
    }
}
