<?php

    namespace Modules\Qualification\Http\Controllers;

    use App\Model\LabelCustomization;
    use App\Organisation;
    use function GuzzleHttp\Promise\all;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\App;
    use Validator;
    use Auth;
    use DB;
    use Modules\Qualification\Services\RegistrationService;
    use Modules\Qualification\Entities\Prospect;
    use Modules\Qualification\Entities\QualificationClients;
    use Modules\Qualification\Entities\UserDomain;
    use Hyn\Tenancy\Models\Hostname;
    use App\User;
    use App\Entity;
    use App\EntityUser;
    use App\Workshop;
    use App\WorkshopMeta;

    class RegistrationController extends Controller
    {
        public function __construct()
        {
            $this->registrationService = RegistrationService::getInstance();
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            // set locale for localization
            App::setLocale((strtolower(session()->get('lang'))) ? strtolower(session()->get('lang')) : 'fr');
        }

        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index(Request $request)
        {
            try {
                $email = '';
                $userid = '';
                if ($request->has('email') && $request->has('userid')) {
                    $email = base64_decode($request->email);
                    $userid = base64_decode($request->userid);
                }

                $user = User::where(['email' => $email, 'sub_role' => 'C1'])->first();
                if (strlen($user->postal) < 5) {
                    $user->postal = str_pad($user->postal, 5, '0', STR_PAD_RIGHT);
                }
                // $domain=UserDomain::where(['user_id'=>$user->id])->get();
                // $company=EntityUser::with('entity')->where(['user_id'=>$user->id])->first();
                $WorkshopMeta = WorkshopMeta::where(['user_id' => $user->id, 'role' => 4])->first();
                if ($WorkshopMeta == NULL) {
                    $workshop = NULL;
                } else {
                    $workshop = Workshop::withoutGlobalScopes()->find($WorkshopMeta->workshop_id);
                    if ($workshop != NULL) {
                        if ($workshop->setting != NULL) {
                            $setting = $workshop->setting;
                            $workshop->workshop_logo = env('AWS_PATH') . $setting['web']['header_logo'];
                            // $workshop->workshop_logo = 'https://s3-eu-west-2.amazonaws.com/ooionline.com/uploads/eFZorOIB9Mgt3caRVWXw5E1UbjZYCv50ECJHCYN7.png';

                        } else {
                            $workshop->workshop_logo = NULL;
                            // $workshop->workshop_logo = 'https://s3-eu-west-2.amazonaws.com/ooionline.com/uploads/eFZorOIB9Mgt3caRVWXw5E1UbjZYCv50ECJHCYN7.png';

                        }
                    }
                }
                $org = Organisation::first(['name_org']);
                $field = $this->registrationService->getStepZeroSkill($user->id);
                // dd($email,$userid,$field);
                $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
                if (in_array($hostname->fqdn, ['cartetppro.ooionline.com', 'cartetppro2.ooionline.com'])) {
                    return view('qualification::index')->with(compact('email', 'userid', 'user', 'field', 'workshop', 'org'));
                } else {
                    return view('qualification::verification_code')->with(compact('email', 'userid', 'user', 'field', 'workshop', 'org', 'hostname'));
                }
            } catch (\Exception $e) {
                abort(404);
                //return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }

        }

        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('qualification::create');
        }

        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @return Response
         */
        public function store(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'zip_code' => 'required',
                    // 'domain' => 'required|exists:hostnames,fqdn',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $workshop = $this->registrationService->getWorkshopData(['zip_code' => $request->zip_code, 'domain' => $request->domain]);
                if ($workshop['workshop']) {
                    return response()->json(['status' => TRUE, 'data' => $workshop]);
                }
                return response()->json(['status' => FALSE, 'msg' => 'Code postal invalide']);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }

        }

        /**
         * Show the specified resource.
         * @return Response
         */
        public function show()
        {
            return view('qualification::show');
        }

        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit()
        {
            return view('qualification::edit');
        }

        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return Response
         */
        public function update(Request $request)
        {
        }

        /**
         * Remove the specified resource from storage.
         * @return Response
         */
        public function destroy()
        {
        }

        public function finalStep(Request $request)
        {
            // try {
            $validator = Validator::make($request->all(), [
                'member_checkbox' => 'required',
                'workshop_id'     => 'required',
                'fname'           => 'required',
                'lname'           => 'required',
                'tel'             => 'required',
                'email'           => 'required|email',
                // 'company' => 'required',
                // 'reg_no' => 'required',
                'zip_code'        => 'required',
                'domain'          => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $getHostname = Hostname::where('fqdn', $request->domain)->first();
//
            $host = $this->tenancy->hostname($getHostname);
            $Prospect = Prospect::where('email', $request->email)->count();
            $QualificationClients = QualificationClients::where('email', $request->email)->count();
            $user = User::where(['email' => $request->email, 'sub_role' => 'C1'])->count();

            if ($Prospect > 0 || $QualificationClients > 0 || $user > 0) {
                return response()->json(['status' => FALSE, 'msg' => (__('message.EMAIL_EXISTS') . ' ' . __('message.DIRECT_CARTE') . ' ' . $request->domain)], 422);
            }
            $workshop = $this->registrationService->saveFinalData($request->all());
            if ($workshop['code'] == 1) {
                // $workshop['status']=true;
                // $workshop['redirectUrl']=url('qualification/registration-process');
                return response()->json($workshop);
            } else {
                // $workshop['status']=false;
                return response()->json($workshop);
            }
            // } catch (\Exception $e) {
            //     // dd($e->getMessage());
            //     DB::rollback();
            //     return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
            // }
        }

        public function checkCode(Request $request)
        {
            // try{
            $validator = Validator::make($request->all(), [
                'id'    => 'required',
                'email' => 'required',
                'code'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $data = $this->registrationService->checkCode($request->all());
//             var_dump($data);die;
            return response()->json($data);
            // } catch (\Exception $e) {
            //    DB::rollback();
            //     return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
            // }
        }

        /*
         * This function is called from action
         * */
        public function pdf($id, $carCount = 0)
        {
            $pdfName = 'Certificat' . time() . '.pdf';
            $pdfUrl = public_path() . 'public' . '/' . 'pdf' . '/' . $pdfName;
            // dd($pdfUrl,public_path());
            // dd($id);
            $url = url('/') . "/qualification/registration/certification/" . $id . '/' . $carCount;

            $command = 'xvfb-run /home/wkhtmltopdf -T 0mm -B 0mm -L 0 -R 0 --orientation portrait --page-size A4 --encoding "UTF-8"' . " $url $pdfUrl 2>&1";
//         dd($command);
            shell_exec($command);

            $headers = [
                'Content-Type' => 'application/pdf',
            ];
            return response()->download($pdfUrl, $pdfName, $headers);
            return redirect('public/pdf/' . $pdfName);
        }

        /*
         * this function is used to view the certificate in browser
         * */
        public function certification($id, $carCount = 0)
        {
            $user = User::with('userSkillCompany', 'userSkillSiret')->whereId($id)->first();
            $field = $this->registrationService->getStepZeroSkillNew($id, $carCount);
            $domain = $field['domain'];
            $field = $field['getAdminStepFields'];

            $host = app(\Hyn\Tenancy\Environment::class)->hostname();
            if (isset($host->id) && in_array($host->id, [2, 'qualifelec.ooionline.com'])) {
                $isQualiflec = 1;
            } else {
                $isQualiflec = 0;
            }
            $otherfield = $this->registrationService->getStepfields($id, $isQualiflec);
            $date = $this->registrationService->getDeliveryDate($id, $carCount);
//            dd($date,$id, $carCount);
            if (strlen($user->postal) < 5) {
                $user->postal = str_pad($user->postal, 5, '0', STR_PAD_LEFT);
            }


            //to get max count array value
            $carCount = ($carCount == 0) ? 0 : ($carCount - 1);
            if (isset($user->userCards[$carCount])) {
                $cardNo = $user->userCards[$carCount]->card_no;
            } else {
                $cardNo = 'XXXXXX';
            }
            // $zipCode = substr(str_replace(' ', '', $user->postal), 0, 2);
            // $workshop = Workshop::where('code1', $zipCode)->where('is_qualification_workshop', '!=', 0)->withoutGlobalScopes()->first();
            $workshopId = WorkshopMeta::where('user_id', $id)->first();
            $workshop = Workshop::where('id', $workshopId->workshop_id)->withoutGlobalScopes()->first();
            if ($workshop != NULL) {
                if ($workshop->setting != NULL) {
                    $setting = $workshop->setting;
                    // var_dump($setting);die;
                    $workshop->workshop_logo = env('AWS_PATH') . $setting['web']['header_logo'];


                } else {
                    $settings_data = getSettingData('pdf_graphic');
                    $settings_data->header_logo = $this->core->getS3Parameter($settings_data->header_logo, 2);
                    $updatedData['header_logo'] = $settings_data->header_logo;
                    $workshop->workshop_logo = $settings_data->header_logo;
                    // $workshop->workshop_logo = 'https://s3-eu-west-2.amazonaws.com/ooionline.com/uploads/eFZorOIB9Mgt3caRVWXw5E1UbjZYCv50ECJHCYN7.png';

                }
            }
            //dd($otherfield);

            if (isset($host->id) && in_array($host->id, [2, 'qualifelec.ooionline.com'])) {
                return view('qualification::certification_qualiflec')->with(compact('cardNo', 'field', 'user', 'workshop', 'otherfield', 'date', 'domain'));
            } else {
                return view('qualification::certification')->with(compact('cardNo', 'field', 'user', 'workshop', 'otherfield', 'date', 'domain'));
            }
            // return view('qualification::certification');

        }


        public function getQualificationWorkshops(Request $request)
        {
            $validator = Validator::make(['term' => $request->term], [
                'term' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $getHostname = Hostname::where('fqdn', $request->domain)->first();
            $host = $this->tenancy->hostname($getHostname);
            $workshop = Workshop::withoutGlobalScopes()->where(function ($a) use ($request) {
                $a->where('code1', 'LIKE', "$request->term%");
                $a->orWhere('workshop_desc', 'LIKE', "%$request->term%");
            })->where('is_qualification_workshop', '!=', 0)->get(['id', 'workshop_name', 'workshop_desc', 'code1']);
            if (!empty($workshop))
                return response()->json(['status' => TRUE, 'data' => $workshop]);
            else
                return response()->json(['status' => FALSE, 'data' => []]);
        }

        public function getLabel(Request $request)
        {
            $validator = Validator::make(['domain' => $request->domain], [
                'domain' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            $getHostname = Hostname::where('fqdn', $request->domain)->first();
            $host = $this->tenancy->hostname($getHostname);
            $label = LabelCustomization::whereIn('name', ['choose_workshop', 'enter_zipcode', 'you_are_member'])->get(['name', 'on_off', 'default_en', 'default_fr', 'custom_en', 'custom_fr']);
            if (!empty($label))
                return response()->json(['status' => TRUE, 'data' => $label]);
            else
                return response()->json(['status' => FALSE, 'data' => []]);
        }

        /**
         * @param Request $request
         * @return mixed
         */
        public function updateStaticData(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'master_type' => 'required',
                    'id'          => 'required',
                    'field'       => 'required',
                    'value'       => 'required',
                ]);
                //validation false return errors

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                if ($request->value == 'null' || $request->value == NULL) {
                    return response()->json(['status' => FALSE, 'msg' => 'The value field is required.'], 422);
                }
                if ($request->master_type == 'user') {
                    if ($request->field == 'address' || $request->field == 'address1') {
                        $json_array = json_decode($request->value, TRUE);
                        if ($json_array !== NULL) {
                            if (isset($json_array['zip_code'])) {
                                $json_array['postal'] = $json_array['zip_code'];
                                unset($json_array['zip_code']);
                            }

                            if (isset($json_array['address1'])) {
                                $json_array['address'] = $json_array['address1'];
                                unset($json_array['address1']);
                            }
                            $data = User::where('id', $request->id)->update($json_array);
                        } else
                            $data = User::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                    } else {
                        $data = User::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                        $data = [trim($request->field) => trim(($request->value != NULL) ? $request->value : '')];
                    }
                } elseif ($request->master_type == 'contact') {
                    if ($request->field == 'address' || $request->field == 'address1') {
                        $json_array = json_decode($request->value, TRUE);
                        if ($json_array !== NULL) {
                            if (isset($json_array['zip_code'])) {
                                $json_array['postal'] = $json_array['zip_code'];
                                unset($json_array['zip_code']);
                            }

                            if (isset($json_array['address1'])) {
                                $json_array['address'] = $json_array['address1'];
                                unset($json_array['address1']);
                            }
                            $data = Contact::where('id', $request->id)->update($json_array);
                        } else
                            $data = Contact::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                    } else {
                        $data = Contact::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                        $data = [trim($request->field) => trim(($request->value != NULL) ? $request->value : '')];
                    }
                } else {
                    $value = $request->value;
                    if ($request->field == 'entity_logo' && $request->hasFile('value')) {

                        $domain = strtok($_SERVER['SERVER_NAME'], '.');
                        $folder = $domain . '/uploads/' . strtolower($request->master_type);
                        $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        $value = $filename;
                    }
                    if ($request->field == 'address' || $request->field == 'address1') {
                        $json_array = json_decode($request->value, TRUE);
                        if ($json_array !== NULL) {
                            if (isset($json_array['address'])) {
                                $json_array['address1'] = $json_array['address'];
                                unset($json_array['address']);
                            }
                            $data = Entity::where('id', $request->id)->update($json_array);
                        } else
                            $data = Entity::where('id', $request->id)->update([trim($request->field) => trim($request->value)]);
                    } else
                        $data = Entity::where('id', $request->id)->update([trim($request->field) => $value]);
                    if ($data) {
                        $data = Entity::with('industry')->find($request->id);
                    }
                }
                if ($data)
                    return response()->json(['status' => TRUE, 'data' => $data], 200);
                else
                    return response()->json(['status' => FALSE, 'data' => 'Something Went Wrong.'], 500);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
            }
        }
    }
