<?php

    namespace Modules\Resilience\Http\Controllers;

    use DB;
    use App\Setting;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use App\Http\Controllers\CoreController;
    use Modules\Resilience\Http\Requests\CreateReinventPageRequest;
    use Modules\Resilience\Http\Requests\UpdateReinventPageRequest;


    /**
     * Class ReinventController
     * @package Modules\Resilience\Http\Controllers
     */
    class ReinventController extends Controller
    {

        private $core;

        public function __construct()
        {
            $this->core = app(CoreController::class);
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function getReinventPage(Request $request)
        {
            $setting = Setting::where('setting_key', "reinvent_page")->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            $settingValue = json_decode($setting->setting_value);
            return response()->json(['data' => $settingValue, 'status' => TRUE], 200);
        }

        /**
         * Remove logo from resource in storage.
         * @param Request $request
         * @return Response
         */
        public function removeReinventPageLogo(Request $request)
        {
            $setting = Setting::where('setting_key', "reinvent_page")->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'Reinvent Page Data Not Found', 'data' => []], 200);
            }
            try {
                DB::connection('tenant')->beginTransaction();
                $setting->forceFill([
                    'setting_value->logo' => NULL,
                ])->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => json_decode($setting->setting_value), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param $image
         * @return void
         */
        public function uploadImageGetUrl($image)
        {
            $imageUrl = '';
            if ($image) {
                $hostname = $this->tenancy->hostname()['fqdn'];
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $filePath = env('AWS_BUCKET') . '/' . $hostname . '/consultation/reinvent/bottom/' . $fileName;
                $imageUrl = ($this->core->fileUploadToS3($filePath, ($image), 'public'));
            }
            return $imageUrl;
        }


        /**
         * Store a newly created resource in storage.
         * @param UpdateReinventPageRequest $request
         * @return void
         */
        public function update(UpdateReinventPageRequest $request)
        {
            $setting = Setting::where('setting_key', "reinvent_page")->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'Reinvent Page Not Found', 'data' => []], 200);
            }
            $data = $request->all();
            $regexHttp = "~^(?:f|ht)tps?://~i";
            if ($request->has('website') && !empty($request->website) && !preg_match($regexHttp, $request->website)) {
                $data['website'] = "https://" . $request->website;

            }
            if ($request->has('twitter') && !empty($request->twitter) && !preg_match($regexHttp, $request->twitter)) {
                $data['twitter'] = "https://" . $request->twitter;

            }
            if ($request->has('facebook') && !empty($request->facebook) && !preg_match($regexHttp, $request->facebook)) {
                $data['facebook'] = "https://" . $request->facebook;

            }
            if ($request->has('linkedin') && !empty($request->linkedin) && !preg_match($regexHttp, $request->linkedin)) {
                $data['linkedin'] = "https://" . $request->linkedin;

            }
            if ($request->has('instagram') && !empty($request->instagram) && !preg_match($regexHttp, $request->instagram)) {
                $data['instagram'] = "https://" . $request->instagram;

            }
            if ($request->has('logo') && !empty($request->logo)) {
                $logo = $this->uploadImageGetUrl($request->logo);
                $data['logo'] = $logo;
            }
            if ($request->has('bottomImage') && !empty($request->bottomImage)) {
                $bottomImage = $this->uploadImageGetUrl($request->bottomImage);
                $data['bottomImage'] = $bottomImage;
            }
            unset($data['_method']);
            $jsonKeys = ["headerBackgroundColor", "headerTextColor", "runningManColor", "lightTextColor", "mediumTextColor", "darkTextColor", "highlightTextColor", "lightBackGroundColor", "darkBackGroundColor", "highlightBackGroundColor", "mediumBackGroundColor", "bottomTextColor", "shapeBackColor", "shapeActiveBackColor", "stickerGradiantLeftColor", "stickerActiveGradiantLeftColor", "stickerActiveGradiantRightColor", "stickerGradiantRightColor", "stickerTextColor", "stickerActiveTextColor", "shapeTextColor", "shapeActiveTextColor", "circleGradiantLeftColor", "circleGradiantRightColor", "circleActiveGradiantLeftColor", "circleActiveGradiantRightColor","footerTextColor"];
            try {
                DB::connection('tenant')->beginTransaction();
                $updateArr = [];
                // Replace 2 or more breaks in a multi-line string with a single break.
                $data['sectionLineOne'] = preg_replace('#(<\s*br[^/>]*/?\s*>\s*){2,}#is', "<br />\n", $data['sectionLineOne']);
                $data['sectionLineTwo'] = preg_replace('#(<\s*br[^/>]*/?\s*>\s*){2,}#is', "<br />\n", $data['sectionLineTwo']);
                $data['footerTextLineOne'] = preg_replace('#(<\s*br[^/>]*/?\s*>\s*){2,}#is', "<br />\n", $data['footerTextLineOne']);
                $data['footerTextLineTwo'] = preg_replace('#(<\s*br[^/>]*/?\s*>\s*){2,}#is', "<br />\n", $data['footerTextLineTwo']);
                foreach ($data as $k => $v) {
                    $updateArr['setting_value->' . $k] = in_array($k, $jsonKeys) ? json_decode($v) : $v;
                }
                $setting->forceFill($updateArr)->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => json_decode($setting->setting_value), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function getSignInPage(Request $request)
        {
            $key = 'resilience_signin_page';
            return $this->getSetting($key);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function getSignUpPage(Request $request)
        {
            $key = 'resilience_signup_page';
            return $this->getSetting($key);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * this function update the signIn page setting
         */
        public function updateSignIn(Request $request)
        {
            $setting = Setting::where('setting_key', "resilience_signin_page")->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'Reinvent Page Not Found', 'data' => []], 200);
            }
            $data = $request->all();

            if ($request->has('logo') && !empty($request->logo)) {
                $logo = $this->uploadImageGetUrl($request->logo);
                $data['logo'] = $logo;
            }

            unset($data['_method']);
            $jsonKeys = ["mainBackgroundColor", "mainTextColor", "titleTextColor1", "titleTextColor2", "alternateColor"];
            try {
                DB::connection('tenant')->beginTransaction();
                $updateArr = [];
                foreach ($data as $k => $v) {
                    $updateArr['setting_value->' . $k] = in_array($k, $jsonKeys) ? json_decode($v) : $v;
                }
                $setting->forceFill($updateArr)->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => json_decode($setting->setting_value), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * this function update the signUp page setting
         */
        public function updateSignUp(Request $request)
        {
            $setting = Setting::where('setting_key', "resilience_signup_page")->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'Reinvent Page Not Found', 'data' => []], 200);
            }
            $data = $request->all();

            if ($request->has('logo') && !empty($request->logo)) {
                $logo = $this->uploadImageGetUrl($request->logo);
                $data['logo'] = $logo;
            }

            unset($data['_method']);
            $jsonKeys = ["mainBackgroundColor", "mainTextColor", "titleTextColor1", "titleTextColor2", "alternateColor"];
            try {
                DB::connection('tenant')->beginTransaction();
                $updateArr = [];
                foreach ($data as $k => $v) {
                    $updateArr['setting_value->' . $k] = in_array($k, $jsonKeys) ? json_decode($v) : $v;
                }
                $setting->forceFill($updateArr)->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => json_decode($setting->setting_value), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param Request $request
         * this function update Forgot password page Setting
         */
        public function updateForgotPage(Request $request)
        {
            $jsonKeys = ["ForgotWelcomeTextLine1", "ForgotWelcomeTextLine2", "ForgetPasswordButtonText", "ForgotCaptionText", "ForgotPageTextLine1", "ForgotPageTextLine2", "ForgotPageText"];

            return $this->updateSetting('resilience_forgot_page', $request, $jsonKeys);
        }

        /**
         * @param Request $request
         * this function update verification password page Setting
         */
        public function updateVerificationPage(Request $request)
        {
            $jsonKeys = ["VerificationCodeWelcomeTextLine1", "VerificationCodeWelcomeTextLine2", "VerificationCodeButtonText", "VerificationCodeCaptionText", "VerificationCodePageTextLine1", "VerificationCodePageTextLine2", "VerificationCodePageText"];

            return $this->updateSetting('resilience_verification_page', $request, $jsonKeys);
        }

        /**
         *this function is used to send setting for forgot page
         */
        public function getForgotSetting()
        {
            $key = 'resilience_forgot_page';
            $signUp = Setting::where('setting_key', 'resilience_signup_page')->first();
            if (!$signUp) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            $decode = json_decode($signUp->setting_value);
            $mergeAny['mainBackgroundColor'] = (isset($decode->mainBackgroundColor) && !empty($decode->mainBackgroundColor)) ? $decode->mainBackgroundColor : NULL;
            $mergeAny['titleTextColor1'] = (isset($decode->titleTextColor1) && !empty($decode->titleTextColor1)) ? $decode->titleTextColor1 : NULL;
            $mergeAny['titleTextColor2'] = (isset($decode->titleTextColor2) && !empty($decode->titleTextColor2)) ? $decode->titleTextColor2 : NULL;
            $mergeAny['alternateColor'] = (isset($decode->alternateColor) && !empty($decode->alternateColor)) ? $decode->alternateColor : NULL;
            $mergeAny['logo'] = isset($decode->logo) ? $decode->logo : NULL;
            return $this->getSetting($key, $mergeAny);
        }

        /**
         *this function is used to send setting for verification page
         */
        public function getVerificationSetting()
        {
            $key = 'resilience_verification_page';
            $signUp = Setting::where('setting_key', 'resilience_signup_page')->first();
            if (!$signUp) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            $decode = json_decode($signUp->setting_value);
            $mergeAny['mainBackgroundColor'] = (isset($decode->mainBackgroundColor) && !empty($decode->mainBackgroundColor)) ? $decode->mainBackgroundColor : NULL;
            $mergeAny['titleTextColor1'] = (isset($decode->titleTextColor1) && !empty($decode->titleTextColor1)) ? $decode->titleTextColor1 : NULL;
            $mergeAny['titleTextColor2'] = (isset($decode->titleTextColor2) && !empty($decode->titleTextColor2)) ? $decode->titleTextColor2 : NULL;
            $mergeAny['alternateColor'] = (isset($decode->alternateColor) && !empty($decode->alternateColor)) ? $decode->alternateColor : NULL;
            $mergeAny['logo'] = isset($decode->logo) ? $decode->logo : NULL;
            return $verification = $this->getSetting($key, $mergeAny);

        }

        /**
         * @param $key
         * @param $request
         * @param $jsonKeys
         * @return \Illuminate\Http\JsonResponse
         * this function update the setting for setting table we need to pass above keys
         */
        protected function updateSetting($key, $request, $jsonKeys)
        {
            $setting = Setting::where('setting_key', $key)->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'Reinvent Page Not Found', 'data' => []], 200);
            }
            $data = $request->all();

            if ($request->has('logo') && !empty($request->logo)) {
                $logo = $this->uploadImageGetUrl($request->logo);
                $data['logo'] = $logo;
            }

            unset($data['_method']);
            try {
                DB::connection('tenant')->beginTransaction();
                $updateArr = [];
                foreach ($data as $k => $v) {
                    $updateArr['setting_value->' . $k] = in_array($k, $jsonKeys) ? ($v) : $v;
                }
                $setting->forceFill($updateArr)->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => json_decode($setting->setting_value), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param $key
         * @return \Illuminate\Http\JsonResponse
         * this function get the setting from setting table we need to pass key only
         */
        protected function getSetting($key, $mergeAny = NULL)
        {
            $setting = Setting::where('setting_key', $key)->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            if (isJson($setting->setting_value)) {
                $settingValue = json_decode($setting->setting_value);
            } else {
                $settingValue = json_decode(preg_replace('/\s+/', '', $setting->setting_value));
            }
            if (!empty($mergeAny)) {
                $settingValue = collect($settingValue)->merge(collect($mergeAny));
            }
            return response()->json(['data' => $settingValue, 'status' => TRUE], 200);
        }
    }
