<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CustomJsonException extends Exception {
    
    /**
     * @var JsonResponse
     */
    private $response;
    
    public function __construct(JsonResponse $response) {
        parent::__construct();
        $this->response = $response;
    }
    
    public function render() {
        return $this->response;
    }
}
