<?php


namespace Modules\Cocktail\Services\Contracts;


interface ApiExecuteFactory {
    
    const QUERY_PARAMETER = 1;
    const FORM_DATA_PARAMETER = 2;
    const RAW_JSON = 3;
    
    /**
     * @param $url
     * @param $data
     * @return string
     */
    public function executeGET($url, $data=[]);
    
    /**
     * @param $url
     * @param $data
     * @param $dataSource
     * @return string
     */
    public function executePOST($url, $data, $dataSource);
    
    /**
     * @param $url
     * @param $data
     * @param $dataSource
     * @return string
     */
    public function executePUT($url, $data, $dataSource);
    
    /**
     * @param $url
     * @param $data
     * @param $dataSource
     * @return string
     */
    public function executeDELETE($url, $data, $dataSource);
    
    /**
     * @param $url
     * @param $data
     * @param $dataSource
     * @return mixed
     */
    public function executePATCH($url, $data, $dataSource);
    
}