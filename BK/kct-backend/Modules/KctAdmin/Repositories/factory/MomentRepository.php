<?php


namespace Modules\KctAdmin\Repositories\factory;


use Exception;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Repositories\IMomentRepository;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;


class MomentRepository implements IMomentRepository {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create($param): Moment {
//        if (isset($param['moment_type'])) {
//            if ($param['moment_type'] == 2) {
//                $broadcast = $this->adminServices()->zoomService->createWebinar($param);
//                $param['moment_id'] = $broadcast['moment_id'];
//                $param['moment_settings'] = $broadcast;
//            } else if($param['moment_type'] == 4 || $param['moment_type'] == 3) {
//                $broadcast = $this->adminServices()->zoomService->createMeeting($param);
//                $param['moment_type'] = 4;
//                $param['moment_id'] = $broadcast['moment_id'];
//                $param['moment_settings'] = $broadcast;
//            }
//        }
        return Moment::create($param);
    }

    public function update($id, $param) {
        return Moment::where('id', $id)->update($param);
    }

    public function delete($ids) {
        return Moment::whereIn('id', $ids)->delete();
    }

}
