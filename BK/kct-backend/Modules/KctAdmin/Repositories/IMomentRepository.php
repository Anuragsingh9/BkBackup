<?php

namespace Modules\KctAdmin\Repositories;

use Modules\KctAdmin\Entities\Moment;

interface IMomentRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create moment
     * -----------------------------------------------------------------------------------------------------------------
     * @param $param
     * @return Moment
     */
    public function create($param): Moment;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the event moment
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @param $param
     * @return Moment
     */
    public function update($id, $param);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To delete the multiple event moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $ids
     */
    public function delete($ids);
}
