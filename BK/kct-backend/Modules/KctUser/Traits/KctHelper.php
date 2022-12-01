<?php


namespace Modules\KctUser\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the reusable helper functions globally in Super Admin Module
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Trait KctHelper
 * @package Modules\SuperAdmin\Services
 */
trait KctHelper {
    use \Modules\KctAdmin\Traits\KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the Internal Server Error Exception and return the Json Response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Exception $exception
     * @return JsonResponse
     */
    public function handleIse(Exception $exception): JsonResponse {
        return response()->json([
            'status' => false,
            'msg'    => $exception->getMessage(),
            'trace'  => $exception->getTrace(),
        ], 500);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the 422 response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $message
     * @param array $errors
     * @return JsonResponse
     */
    public function send422($message, $errors = []): JsonResponse {
        return response()->json(['status' => false, 'message' => $message, 'errors' => $errors], 422);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the 422 response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $message
     * @return JsonResponse
     */
    public function send403($message): JsonResponse {
        return response()->json(['status' => false, 'message' => $message], 403);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to convert the date and time to carbon object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $date
     * @param string|null $time
     * @return Carbon|false
     */
    public function getCarbonByDateTime(string $date, ?string $time = null) {
        return Carbon::createFromFormat(
            config('kctadmin.constants.dateFormat'),
            $time ? "$date $time" : $date
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for converting rgba color code into hex color code
     * -----------------------------------------------------------------------------------------------------------------
     * @param $rgba
     * @return string
     */
    public function rgbaToHex($rgba): string {
        $hex = sprintf("#%02x%02x%02x", $rgba['r'], $rgba['g'], $rgba['b']);
        $alphaDec = $rgba['a'] * 255;
        $alpha = dechex($alphaDec);
        return $hex . $alpha;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for converting hex color code into rgba color code
     * -----------------------------------------------------------------------------------------------------------------
     * @param $hex
     * @return array
     * @throws Exception
     */
    public function hexToRgba($hex): array {
        $hexValue = str_replace('#', '', $hex);
        $length = strlen($hexValue);
        if ($length == 6) {
            $hexValue .= "FF";
        } else if ($length == 3) {
            $hexValue = "$hexValue[0]$hexValue[0]$hexValue[1]$hexValue[1]$hexValue[2]$hexValue[2]";
        } else if ($length < 6) {
            throw new Exception('Invalid color code');
        }
        $alpha = hexdec(substr($hexValue, 6, 2)) / 255;
        $alpha = round($alpha, 2);
        return [
            'r' => hexdec("$hexValue[0]$hexValue[1]"),
            'g' => hexdec("$hexValue[2]$hexValue[3]"),
            'b' => hexdec("$hexValue[4]$hexValue[5]"),
            'a' => $alpha,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the color from setting value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $settings
     * @param $key
     * @return array
     * @throws Exception
     */
    public function getColorFromSetting($settings, $key): array {
        $value = $settings->where('setting_key', $key)->first();
        if (isset($value->setting_value[$key])) {
            $mainColor1 = $this->hexToRgba($value->setting_value[$key]);
        } else {
            throw new Exception("Invalid Color for $key");
        }
        return $mainColor1;
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the color from setting value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $settings
     * @param $key
     * @return int
     */
    public function getCheckFromSetting($settings, $key): int {
        $value = $settings->where('setting_key', $key)->first();
        if (isset($value->setting_value[$key])) {
            return $value->setting_value[$key] ? 1 : 0;
        }
        return 0;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the subdomain name from request
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string
     */
    public function getHostFromRequest(): string {
        $subDomain = explode('.', request()->getHost());
        if (count($subDomain) > 1) {
            $subDomain = $subDomain[0];
        } else {
            $subDomain = '';
        }
        $subDomain = $subDomain != '' ? "$subDomain." : $subDomain;
        return $subDomain . env('APP_FRONT_HOST');
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * @description Get the random string for forget password
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $length
     * @return string
     */
    public function randomString(int $length = 10): string {
        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To convert the previous entity type to new entity type
     * previous
     * 2 = company, 3 union
     *
     * now 1 company, 2 union
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $previousType
     * @return int
     */
    public function castEntityTypeToNew($previousType): int {
        return $previousType == 2 ? 1 : 2;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if current time is between start and end time or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $start
     * @param $end
     * @return bool
     */
    public function isBetweenCurrentTime($start, $end): bool {
        $start = $this->getCarbonByDateTime($start);
        $end = $this->getCarbonByDateTime($end);
        $current = Carbon::now();
        return $start <= $current && $current < $end;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will encrypt the given data
     * -----------------------------------------------------------------------------------------------------------------
     * @param $string
     * @return string
     */
    public function encryptData($string): string {
        return Crypt::encryptString($string);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will decrypt the given data
     * -----------------------------------------------------------------------------------------------------------------
     * @param $string
     * @return string
     */
    public function decryptData($string): string {
        return Crypt::decryptString($string);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for cropping image according to parameters provided
     * -----------------------------------------------------------------------------------------------------------------
     * @param $image
     * @param $width
     * @param $height
     * @return mixed
     */
    public function cropImage($image, $width, $height) {
        return $image->crop($width, $height);
    }
}
