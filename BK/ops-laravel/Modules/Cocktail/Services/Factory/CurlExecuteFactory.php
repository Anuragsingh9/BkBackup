<?php


namespace Modules\Cocktail\Services\Factory;


use App\AccountSettings;
use App\Setting;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\Contracts\ApiExecuteFactory;

class CurlExecuteFactory implements ApiExecuteFactory {
    
    private $url;
    
    /**
     * @param $url
     * @param $data
     * data must have
     * headers
     * data
     * @return string|void
     */
    public function executeGET($url, $data = []) {
        $headers = isset($data['headers']) ? $headers = $data['headers'] : [];
        
        if (isset($data['data']) && count($data['data'])) {
            $url .= "?" . http_build_query($data['data']);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // to send that we need the transfer data back from server
        if (count($headers)) {
            // include header only if passed
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    
    /**
     * To execute the post api with data and header
     *
     * @param $url
     * @param $data
     * @param $dataSource
     * @return bool|string
     */
    public function executePOST($url, $data, $dataSource) {
        return $this->execute($url, $data, $dataSource, "POST");
    }
    
    /**
     * To execute put api
     *
     * @param $url
     * @param $data
     * @param $dataSource
     * @return bool|string
     */
    public function executePUT($url, $data, $dataSource) {
        return $this->execute($url, $data, $dataSource, "PUT");
    }
    
    /**
     * To execute the delete api
     *
     * @param $url
     * @param $data
     * @param $dataSource
     * @return bool|string
     */
    public function executeDELETE($url, $data, $dataSource) {
        return $this->execute($url, $data, $dataSource, 'DELETE');
    }
    
    /**
     * To execute put api
     *
     * @param $url
     * @param $data
     * @param $dataSource
     * @return bool|string
     */
    public function executePATCH($url, $data, $dataSource) {
        return $this->execute($url, $data, $dataSource, "PATCH");
    }
    
    
    
    /**
     * This method wil actually execute the api with proper data passed
     * the data source will decide how data will send like form data, raw json or query string
     *
     * @param $url
     * @param $data
     * @param $dataSource
     * @param $method
     * @return bool|string
     */
    private function execute($url, $data, $dataSource, $method) {
        $this->url = $url;
        $headers = [];
        
        $data = $this->prepareData($data, $dataSource);
        
        if (isset($data['headers'])) {
            $headers = $data['headers'];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            // if any other method
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        // the actual data is in data ->data
        if (isset($data['data']) && $data['data']) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
        }
        // to send that we need the transfer data back from server
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (count($headers)) {
            // include header only if passed
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($ch);
        // for safe close the curl
        curl_close($ch);
        return $output;
    }
    
    /**
     * This method is responsible for preparing data according to send type
     * like for raw json it will decode the data and add the header content type is json
     *
     * @param $data
     * @param $dataSource
     * @return mixed
     */
    private function prepareData($data, $dataSource) {
        $header = isset($data['headers']) ? $data['headers'] : [];
        
        // For the form data we can send it directly
        switch ($dataSource) {
            case self::QUERY_PARAMETER:
                $data['data'] = http_build_query($data['data']);
                $this->url .= "?{$data['data']}";
                $data['data'] = null;
                break;
            case self::RAW_JSON:
                // if header not contain we will add content type is json sending
                $contentType = "Content-Type:application/json";
                // searching if already contains this header or not
                if (!in_array(strtolower($contentType), array_map('strtolower', $header))) {
                    $data['headers'][] = $contentType;
                }
                // encoding data so data will be send as json
                $data['data'] = isset($data['data']) ? json_encode($data['data']) : '{}';
                break;
            default;
        }
        return $data;
    }
}