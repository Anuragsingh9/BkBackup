<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;

use Exception;
use Modules\KctAdmin\Repositories\BaseRepo;
use Modules\KctAdmin\Services\BusinessServices\ISpaceService;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use phpDocumentor\Reflection\Types\Mixed_;

class SpaceService implements ISpaceService {
    use ServicesAndRepo;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To search for space host for an event space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $val
     * @param $eventUuid
     * @return Users;
     */
    public function searchSpaceHost($eventUuid, $val) {
        if (strlen($val) >= 3) {
            // $data = User::select('id', 'fname', 'lname', 'email', 'avatar')
            //     ->where(function ($a) use ($val) {
            //         $a->where('fname', 'like', '%' . $val . '%');
            //         $a->orWhere('lname', 'like', '%' . $val . '%');
            //         $a->orWhere('email', 'like', '%' . $val . '%');
            //         $a->orWhere(DB::raw("CONCAT(fname, ' ', 'lname')"), 'like', '%' . $val . '%');
            //     })
            //     ->whereNotIn('email', $this->getAllHosts($eventUuid))
            //     ->groupBy('email')->get();
            $data = $this->userRepository->searchByNameEmail($val);
            $data = $this->userRepository->filterByExcludeEmail($data, $this->getAllHosts($eventUuid));
            return $this->userRepository->groupByEmail($data)->get();
        }
    }

}
