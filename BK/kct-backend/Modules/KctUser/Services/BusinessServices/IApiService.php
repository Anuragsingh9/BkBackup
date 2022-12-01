<?php

namespace Modules\KctUser\Services\BusinessServices;

interface IApiService {
    /**
     * @param $url
     * @param $data
     * @return string
     */
    public function executeGET($url, $data = []): string;

    /**
     * @param $url
     * @param $data
     * @param $dataSource
     * @return string
     */
    public function executePOST($url, $data, $dataSource): string;
}
