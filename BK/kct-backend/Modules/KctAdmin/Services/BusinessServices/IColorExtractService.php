<?php
namespace Modules\KctAdmin\Services\BusinessServices;

interface IColorExtractService {

    /**
     * @param $image
     * @return mixed
     */
    public function getMainColors($image);
}
