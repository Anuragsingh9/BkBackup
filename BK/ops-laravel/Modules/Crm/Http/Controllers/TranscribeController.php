<?php

namespace Modules\Crm\Http\Controllers;

use App\AccountSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Crm\Entities\TranscribeTracking;

class TranscribeController extends Controller {

    public $PERIODIC_TIME = 15;
    protected $tenancy;

    protected $key;
    protected $secret;
    protected $region;

    public function __construct() {
        $this->key = 'AKIAR3VLLS2N4JU4UQGP';
        $this->secret = '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';
        $this->region = 'eu-west-1';
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
    }

    public function getKey() {
        return $this->key;
    }

    public function getRegion() {
        return $this->region;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getSignature(Request $request) {
        $validator = Validator::make($request->all(), [
            'stringToSign' => 'required|string',
        ]);

        if ($validator->fails()) {
            return NULL;
        }

        $h1 = $this->hmac('AWS4' . $this->secret, date('Ymd')); // date-key
        $h2 = $this->hmac($h1, $this->region); // region-key
        $h3 = $this->hmac($h2, 'transcribe'); // service-key
        $h4 = $this->hmac($h3, 'aws4_request'); // signing-key
        $stringToSing = str_replace("**", "\n", $request->stringToSign);
        return $this->hmac($h4, $stringToSing, FALSE); // encoding false to make it hex encoding
    }

    public function hmac($key, $string, $encoding = TRUE) {
        return hash_hmac('sha256', $string, $key, $encoding);
    }

    public function saveBlob(Request $request) {
        if ($request->has('audio_data')) {
            $save_path = '/ooionline.com/' . $this->tenancy->hostname()['fqdn'] . '/Transcribe/' . date('Y/m') . '/' . Auth::user()->id . time() . '.wav';
            $s3 = Storage::disk('s3');
            $s3->put($save_path, $request->audio_data);
            return $s3->url($save_path);
        } else {
            return response()->json(['status' => FALSE, 'data' => ''], 500);
        }
    }

    /**
     * @param $type
     * @return bool|JsonResponse
     */
    public function startPeriodicLogGenerate($type) {
        try {

            $track = TranscribeTracking::create([
                'account_id' => $this->tenancy->hostname()['id'],
                'user_id'    => Auth::user()['id'],
                'type'       => $type,
                'time_used'  => $this->PERIODIC_TIME,
                'used_at'    => date('Y-m-d h:i:s')
            ]);
            $credit = $this->reduceAvailableCredit();
            return response()->json(['status' => TRUE, 'data' => ['log_id' => $track->id, 'available_credit' => $credit]], 200);

        } catch (\Exception $e) {
            return FALSE;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * 1. Check if its for new log or previous logs
     * 2. if its new
     *          create a log entry and return its id
     * 3. if id previously present
     *          check its latest of log id of current user
     */
    public function periodicLogGenerate(Request $request) {
        $type = ($request->has('type') && $request->type == 1) ? $request->type : 2;
        if (!$request->has('id')) {
            return $this->startPeriodicLogGenerate($type);
        } else {
            $todayLatestLog = TranscribeTracking::where([
                ['used_at', 'like', date('Y-m-d') . '%'],
                ['user_id', Auth::user()->id],
                ['account_id', $this->tenancy->hostname()['id']],
            ])->orderBy('id', 'desc');
            if (!$todayLatestLog->count() || ($todayLatestLog->first()->id != $request->id)) {
                return $this->startPeriodicLogGenerate($type);
            } else {
                $trackTimeUsed = $todayLatestLog->first()->time_used;
                $timeUsed = $trackTimeUsed + $this->PERIODIC_TIME;
                if (!($todayLatestLog->update(['time_used' => $timeUsed]))) {
                    return $this->startPeriodicLogGenerate($type);
                }
                $credit = $this->reduceAvailableCredit();
                return response()->json(['status' => TRUE, 'data' => ['log_id' => $todayLatestLog->first()->id, 'available_credit' => $credit]], 200);
            }
        }
    }

    public function reduceAvailableCredit() {
        $accountSetting = AccountSettings::where('account_id', $this->tenancy->hostname()['id'])->first();
        if (isset($accountSetting->setting['transcribe_setting'])) {
            $credit = $accountSetting->setting['transcribe_setting']['available_credit'];
            if ($credit < $this->PERIODIC_TIME) {
                $newCredit = 0;
            } else {
                $newCredit = $credit - $this->PERIODIC_TIME;
            }
            AccountSettings::where('account_id', $this->tenancy->hostname()['id'])
                ->update(['setting->transcribe_setting->available_credit' => $newCredit]);
            return $credit;
        }
    }
}