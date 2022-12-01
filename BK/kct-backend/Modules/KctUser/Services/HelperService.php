<?php


namespace Modules\KctUser\Services;


class HelperService {
    /**
     * @param int $length
     * @param null $type
     * @param string $characters
     * @return string
     */
    function generateRandomValue($length = 10, $type = NULL, $characters = "0123456789") {
        if (!empty($type)) {
            $characters = '1234';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function setPasscode($hostCode, $randCode) {

//    if(substr($hostCode,0,1)>=5){
//        $hostCode=generateRandomValue(1,5).substr($hostCode,1);  //var_dump($hostCode);exit;
//    }
        $userCode = substr($hostCode, 0, 2) . substr($randCode, 0, 3) . substr($hostCode, 2, 1) . substr($randCode, 3, 1) . substr($hostCode, 3, 1);
        //left shift
        $hashCode = $userCode << 1;
        return ['userCode' => $userCode, 'hashCode' => $hashCode];
    }

    function genRandomNum($length) {
        $string = '1928374656574839232764126534';
        $string_shuffled = str_shuffle($string);
        $otp = substr($string_shuffled, 1, $length);
        return $otp;
    }


}
