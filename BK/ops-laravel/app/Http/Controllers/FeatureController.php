<?php

    namespace App\Http\Controllers;

    use App\User;
    use Sinergi\BrowserDetector\Browser;
    use Sinergi\BrowserDetector\Os;
    use Sinergi\BrowserDetector\Device;
    use App\StaffLogin;
    use App\SuperadminLogin;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Validator;

    class FeatureController extends Controller
    {
        private $core;

        public function __construct(\App\Http\Controllers\CoreController $coreController)
        {
            $this->core = $coreController;
        }

        public function featureMails(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'mail_data' => 'required_if:mail_for,1,2,3|array',
                'mail_for'  => 'required',
//                'from_os'      => 'required_if:mail_for,==,3',
//                'from_device'  => 'required_with:from_os',
//                'from_browser' => 'required_with:from_os,from_device',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }

            $subject = $this->prepareSubject($request->mail_for);
            $body = $this->prepareBody($request->mail_for, $request->mail_data);
            $footer = $this->prepareFooter($request->mail_for, $request->all());
            if ($request->mail_for == 3)
                $emails = StaffLogin::pluck('email')->toArray();
            elseif (in_array($request->mail_for, [5, 6])) {
                $emails = User::where('role', 'M1')->first(['email'])->toArray();
                if (!empty($emails))
                    $emails = array_values($emails);
                else
                    $emails = [];
            } else
                $emails = SuperadminLogin::pluck('email')->toArray();

            $mailData['mail'] = ['subject' => $subject, 'body' => $body, 'footer' => $footer, 'emails' => $emails];
            $res = $this->core->SendMassEmail($mailData, 'featureMailMsg');
            if ($res) {
                return response()->json(['status' => TRUE, 'data' => $res], 200);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Email not sent'], 500);

            }
            return $res;
        }

        protected function prepareSubject($mailFor = 1)
        {
            switch ($mailFor) {
                case 1:
                    return trans('message.feature_sub');
                    break;
                case 2:
                    return trans('message.module_sub');
                    break;
                case 3:
                    return trans('message.support_sub');
                    break;
                case 4:
                    return trans('message.share_new');
                    break;
                case 5:
                    return trans('message.feature_sub');
                    break;
                case 6:
                    return trans('message.support_sub');
                    break;
                default:
                    return trans('message.feature_sub');
                    break;
            }
        }

        protected function prepareBody($mailFor = 1, $data)
        {
            $array = $data;
            if ($mailFor == 1) {
                array_walk($array, function (&$value, $key) {
                    $value = trans('message.feature') . ' ' . ($key + 1) . '<br> ' . $value;
                });
            } elseif ($mailFor == 4) {
                $hostname = app(\Hyn\Tenancy\Environment::class)->hostname()->fqdn;
                $array[] = str_replace('<here>', '<a href="https://' . $hostname . '">' . config("accountName") . '</a>', trans('message.mail_4'));
            }
            return $array;
        }

        protected function prepareFooter($mailFor = 1, $data)
        {
            $array = [];
            if ($mailFor == 3) {
                $browser = new Browser();
                $os = new Os();
                $device = new Device();
                $array['from_os'] = $os->getName();
                if ($device->getName() != 'unknown') {
                    $array['from_device'] = $device->getName();
                }
                $array['from_browser'] = $browser->getName();
            }
            if (isset(Auth::user()->fname))
                $fromUser = Auth::user()->fname . ' ' . Auth::user()->lname . ',' . Auth::user()->email;
            else
                $fromUser = request()->user()->fname . ' ' . request()->user()->lname . ',' . request()->user()->email;
            $array['from_user'] = $fromUser;
            $array['sent_on'] = getCreatedAtAttribute(Carbon::now());
            return $array;
        }
    }
