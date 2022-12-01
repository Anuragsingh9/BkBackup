<?php

    namespace App\Http\Controllers;

    use Hyn\Tenancy\Models\Hostname;
    use iContact\iContactApi;
    use App\Milestone;
    use App\Project;
    use App\Task;

    use Carbon\Carbon;
    use Illuminate\Support\Facades\App;
    use View, Validator;
    use Illuminate\Http\Request;
    use PHPMailerAutoload;
    use Illuminate\Support\Facades\Storage;
    use League\Flysystem\MountManager;
    use AWS;
    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;
    use PHPMailer\PHPMailer\PHPMailer;
    use DB,
        File,
        Auth;
    use App\User;
    use App\RolePermission;
    use App\Issuer;
    use App\DocumentType;
    use App\Meeting;
    use App\RegularDocument;
    use App\Workshop,
        App\WorkshopMeta;
    use Exception;

    class CoreController extends Controller
    {
        private $tenancy;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);

        }

        public function generateSeeds(Request $request)
        {
            $dataList['cols'] = DB::connection('tenant')->getSchemaBuilder()->getColumnListing($request->table);
            $dataList['data'] = DB::connection('tenant')->table($request->table)->get();
            return view('seeds', $dataList);
        }

        function file_upload($file, $path, $type = NULL)
        {
            $filename = '';
            File::isDirectory($path) or File::makeDirectory($path, 0777, TRUE, TRUE);
            if ($file != '') {
//$filename=md5($file->getClientOriginalName()).time().'.'.$file->getClientOriginalExtension();
                $filename = $file->getClientOriginalName();
                $file->move($path, $filename);
            }
            return $filename;
        }

        function unlinkImg($path, $img)
        {
            if ($img != NULL || $img != '') {
                $image_path = app()->basePath('public/' . $path . $img);
//$image_path_thumb = public_path($path."thumb_".$img);

                if (File::exists($image_path))
                    unlink($image_path);

                /* if(File::exists($image_path_thumb))
                  unlink($image_path_thumb); */
            }
        }

        public function SendEmail($data, $template = NULL)
        {
            //var_dump($data);exit;
            // return 1;
            //checking that email sending is enabled or not
            $authorizeForMail = $this->authorizeForMail();
            if (isset($authorizeForMail->email_enabled) && $authorizeForMail->email_enabled == 0) {
                return 1;
            }

            $mail = $this->mailConfig();
            $mail->addAddress($data['mail']['email']);
            if ($template != NULL) {
                $mail->Subject = (($data['mail']['subject']));
                //$mail->Body =  utf8_encode(view('email_template.' . $template, $data));
                $mail->Body = (View::make('email_template.' . $template, $data)->render());
            } else {
                $mail->Subject = ($data['mail']['subject']);
                $mail->Body = ($data['mail']['msg']);
            }
            $mail->isHTML(TRUE);
// $mail->Body    =view('email_template/',$data);
// $myfile = fopen("emailerror.txt", "w");
            if (!$mail->Send()) {
//            return "<script>alert('Mailer Error: " . $mail->ErrorInfo . "')</script>";//exit;
                $txt = FALSE;
            } else {
//echo "<script>alert('Mail Send.')</script>";
                $txt = TRUE;
            }
            return $txt;
        }

        function mailConfig()
        {
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
//            $mail->SMTPDebug =4;
            header("Content-type:text/html; charset: iso-8859-1");
            $mail->SMTPAuth = TRUE;
            $mail->Host = env('host');
            $mail->Username = env('username');
            $mail->Password = env('password');
            $mail->SMTPSecure = env('smtp_secure');
            $mail->Port = env('smtp_port');
            $mail->FromName = env('from_name');
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => FALSE,
                    'verify_peer_name'  => FALSE,
                    'allow_self_signed' => TRUE,
                ],
            ];

//$mail->From = explode('.',$_SERVER['HTTP_HOST'])[0].'@'.config('constants.EMAIL_SUFFIX');
            $mail->From = env('from_email');

            $mail->addReplyTo(env('reply_to'), 'Email Notification');
            return $mail;
        }


        public function SendMassEmail($data, $template = NULL)
        {
            // return 1;
            //checking that email sending is enabled or not
            $authorizeForMail = $this->authorizeForMail();
            if (isset($authorizeForMail->email_enabled) && $authorizeForMail->email_enabled == 0) {
                return 1;
            }
            $txt = FALSE;
            $flag = 1;
            $mail = $this->mailConfig();
            $mail->Subject = (($data['mail']['subject']));
            // return $data['mail']['emails'];
            // $mail->Subject = utf8_encode(utf8_decode($data['mail']['subject']));


            if (isset($data['mail']['email_to'])) {
                if (!empty($data['mail']['emails'])) {
                    $array_unique = array_unique($data['mail']['emails']);
                    //$mail->SMTPDebug = 2;
                    $array_chunk = array_chunk($array_unique, 40, TRUE);
                    $count_res = count($array_chunk);
                    for ($i = 0; $i < $count_res; $i++) {

                        if (isset($data['mail']['email_to']) && $data['mail']['email_to'] != '' && $flag == 1) {
                            $flag = 0;
                            $mail->clearAddresses();
                            $mail->addAddress($data['mail']['email_to']);
                        }
                        foreach ($array_chunk[$i] as $k => $email_data) {

                            $mail->addBcc($email_data);
                        }
                        //,$mail->getAllRecipientAddresses()
                        // return ($template);
                        if ($template == NULL) {
                            //$mail->Body = ($data['mail']['msg']);
                            $mail->Body = ($data['mail']['msg']);
                        } else {
                            $data['mail']['email'] = $email_data;
                            $mail->Subject = ($data['mail']['subject']);
                            //$mail->Subject = utf8_encode($data['mail']['subject']);
                            $mail->Body = view('email_template.' . $template, $data);
                        }

                        $mail->isHTML(TRUE);

                        $txt = (!$mail->Send()) ? FALSE : TRUE;
                        $mail->ClearAllRecipients();
                    }

                } else {
                    if (isset($data['mail']['email_to']) && $data['mail']['email_to'] != '' && $flag == 1) {
                        $flag = 0;
                        $mail->clearAddresses();
                        $mail->addAddress($data['mail']['email_to']);
                    }
                    if ($template == NULL) {
                        //$mail->Body = ($data['mail']['msg']);
                        $mail->Body = ($data['mail']['msg']);
                    } else {

                        $mail->Subject = ($data['mail']['subject']);
                        //$mail->Subject = utf8_encode($data['mail']['subject']);
                        $data['mail']['email'] = $email_data;
                        $mail->Body = (view('email_template.' . $template, $data)->render());
                    }
                    $mail->isHTML(TRUE);
                    $txt = (!$mail->Send()) ? FALSE : TRUE;
                }
            } else {
                if (is_array($data['mail']['emails'])) {

                    $array_unique = array_unique($data['mail']['emails']);
                    foreach ($array_unique as $k => $email_data) {
                        $mail->addAddress($email_data);

                    }

                    $mail->Subject = ($data['mail']['subject']);
                    //$mail->Subject = utf8_encode($data['mail']['subject']);
                    //$mail->Body = utf8_encode(view('email_template.' . $template, $data));
                    $data['mail']['email'] = $email_data;


                    if ($template == NULL) {
                        //$mail->Body = ($data['mail']['msg']);
                        $mail->Body = ($data['mail']['msg']);
                    } else {
                        $mail->Body = (View::make('email_template.' . $template, $data)->render());
                    }
                    $mail->isHTML(TRUE);
                    $txt = (!$mail->Send()) ? FALSE : TRUE;
                }

                return $txt;
            }
        }

        public function SendGuestMassEmail($data, $template = NULL, $token)
        {

            //checking that email sending is enabled or not
            $authorizeForMail = $this->authorizeForMail();
            if (isset($authorizeForMail->email_enabled) && $authorizeForMail->email_enabled == 0) {
                return 1;
            }
            //return 1;
            $txt = FALSE;
            $flag = 1;
            $mail = $this->mailConfig();

            $mail->Subject = ($data['mail']['subject']);
            //$mail->Subject = utf8_encode($data['mail']['subject']);
            if (!empty($data['mail']['emails'])) {
                $array_unique = array_unique($data['mail']['emails']);

                foreach ($array_unique as $k => $email_data) {
                    $mail->clearAddresses();
                    $mail->addAddress($email_data);
                    if ($token != NULL) {

                        $data['mail']['token'] = $token;
                    }
                    $data['mail']['email'] = $email_data;
                    $mail->Subject = ($data['mail']['subject']);
                    //$mail->Subject = utf8_encode($data['mail']['subject']);
                    //$mail->Body = view('email_template.' . $template, $data);
                    $mail->Body = (View::make('email_template.' . $template, $data)->render());
                    $mail->isHTML(TRUE);
                    $txt = (!$mail->Send()) ? FALSE : TRUE;
                }
            }
            return $txt;
        }

        function checkUserExist($email)
        {
            return User::where('email', $email)->first();
        }

        function getIndustries()
        {
            $industry = DB::connection('tenant')->table('industries')->where(function ($q) {
                $q->whereNotNull('parent')->orWhereRaw('id in(select parent from industries)');
            })->get();

            return response()->json($industry);
        }

        function fileUploadByS3($file, $directory, $permission = NULL)
        {

            $directory = session()->get('domain_name') . '/' . $directory;
            if ($permission) {
                return Storage::disk('s3')->putFile($directory, $file, $permission);
            } else {
                return Storage::disk('s3')->putFile($directory, $file);
            }
        }

        /**
         * file upload to s3
         */
        function fileUploadToS3($filePath, $file, $visibility = 'public')
        {
            $path = Storage::disk('s3')->put(
                $filePath,
                $file, $visibility
            );

            return $path;

        }


        function fileDeleteBys3($file_path)
        {
            return Storage::disk('s3')->delete($file_path);
        }

        function makeDirectoryS3($directory)
        {
            return Storage::disk('s3')->makeDirectory($directory);
        }

        function getS3Parameter($file_path, $type = NULL, $file_name = NULL)
        {
            /*
              $file_path = full file url with folder name
              `   $type =>  1: get file download url, 2: get file view url
              https://s3.ap-south-1.amazonaws.com/ops.sharabh.org/
             */

            $url = '';
            $config['Bucket'] = env('AWS_BUCKET');
            $config['Key'] = $file_path;

            $s3 = Storage::disk('s3');
            if ($s3->exists($file_path)) {
                if ($type == 1) {
                    if ($file_name != NULL) {
                        $config['ResponseContentDisposition'] = 'attachment;filename="' . $file_name . '"';
                    } else {
                        $config['ResponseContentDisposition'] = 'attachment';
                    }
                    $command = $s3->getDriver()->getAdapter()->getClient()->getCommand('GetObject', $config);
                    $requestData = $s3->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+5 minutes');

                    $url = $requestData->getUri();
                    return (string)$url;
                } else {
                    return Storage::disk('s3')->url($file_path);
                }

            }
            return NULL;
        }

        // function getWorkshopByLogin()
        // {
        //     if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
        //         return DB::connection('tenant')->select(DB::raw("select w.id,w.president_id,w.validator_id,w.workshop_name,w.workshop_type,w.is_private,(SELECT fname FROM users where id=w.president_id) as president_fname,(SELECT lname FROM users where id=w.president_id) as president_lname,(SELECT email FROM users where id=w.president_id) as president_email,wm.role,wm.user_id ,w.setting from workshops w left join workshop_metas wm ON wm.workshop_id = w.id WHERE w.display=1 and w.is_qualification_workshop=0 group by w.id order by w.id desc "));
        //     } else {
        //         return DB::connection('tenant')->select(DB::raw("select w.id,w.president_id,w.validator_id,w.workshop_name,w.workshop_type,w.is_private,(SELECT fname FROM users where id=w.president_id) as president_fname,(SELECT lname FROM users where id=w.president_id) as president_lname,(SELECT email FROM users where id=w.president_id) as president_email,wm.role,wm.user_id,w.setting from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . Auth::user()->id . "'And w.display=1 and w.is_qualification_workshop=0 group by w.id"));
        //     }
        // }
        function getWorkshopByLogin()
        {
            if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
                return DB::connection('tenant')->select(DB::raw("select w.id,w.is_qualification_workshop,w.president_id,w.validator_id,w.workshop_name,w.workshop_type,w.signatory,w.setting,w.is_private,(SELECT fname FROM users where id=w.president_id) as president_fname,(SELECT lname FROM users where id=w.president_id) as president_lname,(SELECT email FROM users where id=w.president_id) as president_email,wm.role,wm.user_id,w.setting, w.code1,w.code2,w.display from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where w.display=1 group by w.id  order by CAST(w.code1 AS UNSIGNED) ASC "));
            } else {
                return DB::connection('tenant')->select(DB::raw(" select w.id,w.is_qualification_workshop,w.president_id,w.validator_id,w.workshop_name,w.workshop_type,w.signatory,w.setting,w.is_private,(SELECT fname FROM users where id=w.president_id) as president_fname,(SELECT lname FROM users where id=w.president_id) as president_lname,(SELECT email FROM users where id=w.president_id) as president_email,wm.role,wm.user_id,w.setting,w.code1,w.code2,w.display from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . Auth::user()->id . "' and w.display=1 group by w.id order by CAST(w.code1 AS UNSIGNED) ASC "));
            }
        }


        function getRolesForWorkshop($id)
        {
            $role = WorkshopMeta::where('user_id', Auth::user()->id)->where('workshop_id', $id)->get(['role']);
            $roleKey = Auth::user()->role;
            if (Auth::user()->role == 'M2') {
                if (isset($role[0]->role) && $role[0]->role == 1)
                    $roleKey = 'W0';
                if (isset($role[0]->role) && $role[0]->role == 2)
                    $roleKey = 'W1';
            }
            $role = $this->roleData($roleKey);
            foreach ($role as $key => $value) {
                $data['role'][$value['action_react']] = $value[$roleKey];
            }
            return response()->json($data);
        }

        function roleData($role)
        {
            return RolePermission::get(['action_react', $role, 'title', 'description', 'action_laravel']);
        }

        function getIssuerByLogin()
        {
            return Issuer::get(['issuer_name', 'issuer_code', 'id']);
        }

        function getDocTypeByLogin()
        {
            return DocumentType::get(['document_name', 'document_code', 'id']);
        }

        public function getWorkshopUserRole($wid)
        {
            $res = WorkshopMeta::where('workshop_id', $wid)->where('user_id', Auth::user()->id)->first();
            $data['role'] = $res->role;
            if ($res->role == 0)
                $data['role_name'] = 'member';
            else if ($res->role == 1)
                $data['role_name'] = 'president';
            else if ($res->role == 2)
                $data['role_name'] = 'validator';
            return response()->json($data);
        }

        /* ----------- Start PDF Function ------------- */

        public function prepdPdf($data)
        {

            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($data['wid']);
            $meeting_data = Meeting::find($data['mid']);

            if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                $pdfSeting = $workshop_data->setting['pdf'];
                $settings_data = json_decode(json_encode($pdfSeting));
            } else {
                $settings_data = getSettingData('pdf_graphic');
            }

            $WorkshopName = $this->Unaccent(str_replace(' ', '-', $workshop_data->workshop_name));
            $startDdate = dateConvert($meeting_data->date, 'd M Y');

            $incNumber = getIncrementNumber($data['wid']);
            $file_date = date("d-m-Y", strtotime($meeting_data->date));
            $quotation = $this->Unaccent($workshop_data->code1) . "" . $this->Unaccent($workshop_data->code2) . "-" . date('y') . "" . (str_pad($incNumber, 3, "0", STR_PAD_LEFT));
            $pdf_name = str_replace(' ', '-', $quotation) . '-Ordre-du-jour-' . $WorkshopName . '-' . $file_date . '.pdf';

            $domain = strtok($_SERVER['SERVER_NAME'], '.');
//        $path = config('constants.AWS_PATH') . $domain . '/uploads/';
//        $path = '/public' . $pdf_name;
//        $path = config('constants.AWS_PATH') . $pdf_name;
            $doc_title = $quotation . ' Ordre du jour Réunion ' . $WorkshopName . ' ' . $file_date;
            if (isset($data['version'])) {
                $pdf_name = str_replace(' ', '-', $quotation) . '-Ordre-du-jour-' . $this->Unaccent(str_replace(' ', '-', $WorkshopName)) . '-' . $file_date . $data['version'] . '.pdf';
                $doc_title = $quotation . ' Ordre du jour Réunion ' . $WorkshopName . ' ' . $file_date . $data['version'];
            }
            //this code must be there means below appending version
            $pdfUrl = public_path('public' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdf_name);

            $url = url('/') . "/gen-prepd-pdf/" . $data['mid'] . "/" . $data['wid'].'/'.App::getLocale();
            $footer_url = url('/') . "/prepd-footer/" . $data['mid'] . "/" . $data['wid'];
            $command = 'wkhtmltopdf  --encoding "UTF-8" --footer-font-size "9"  --footer-html ' . $footer_url . '--enable-external-links --margin-bottom 26 --footer-spacing 10' . " $url $pdfUrl 2>&1";
            (shell_exec($command));
            //dd(shell_exec($command));

//        print_r($result);
            return ['pdf_name' => $pdf_name, 'title' => $doc_title, 'inc_number' => $incNumber];
        }

        function Unaccent($string)
        {
            $unwanted_array = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', '÷' => '', 'ü' => 'u', '\'' => '', '’' => '', '‘' => ''];
            $str = strtr($string, $unwanted_array);

            return $str;
        }

        public function prepdFooter($mid, $wid)
        {

            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($wid);
            $user_detail = WorkshopMeta::where('workshop_id', $wid)->where('role', '!=', 0)->get();
            foreach ($user_detail as $key => $value) {
                if ($value->role == 1) {
                    $p_name = $value->user->fname . ' ' . $value->user->lname;
                    $p_email = $value->user->email;
//                $p_email = $value->user->email;
                }
                if ($value->role == 2) {
                    $v_name = $value->user->fname . ' ' . $value->user->lname;
                    $v_email = $value->user->email;
                }
            }

            $meeting_data = Meeting::find($mid);
            if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                $pdfSeting = $workshop_data->setting['pdf'];
                $settings_data = json_decode(json_encode($pdfSeting));
            } else {
                $settings_data = getSettingData('pdf_graphic');
            }
            $WorkshopName = $workshop_data->workshop_name;
            $startDdate = dateConvert($meeting_data->date, 'd M Y');
            $meetingAddress = $meeting_data->place;
            $footer_center_text = str_replace(['[[WorkshopPresidentFullName]]', '[[WorkshopMeetingAddress]]', '[[WorkshopMeetingName]]', '[[WorkshopLongName]]', '[[WorkshopMeetingDate]]', '[[PresidentEmail]]', '[[WorkshopShortName]]', '[[WorkshopvalidatorFullName]]', '[[ValidatorEmail]]', '[[WorkshopShortName]]', '[[WorkshopMeetingTime]]', '[[MessageCategory]]', '[[PresidentPhone]]', '[[ProjectName]]'], [$p_name, $meetingAddress, $meeting_data->name, $WorkshopName, $startDdate, $p_email, $workshop_data->code1, $v_name, $v_email, $workshop_data->code1, '', '', '', ''], @$settings_data->footer_line1);
            // dd($settings_data->footer_line1);

            $footer_line2 = @$settings_data->footer_line2;
            return view('pdf/prepd_footer')->with(['footer_line1' => $footer_center_text, 'footer_line2' => $footer_line2]);
        }

        public function repdFooter($mid, $wid)
        {
            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($wid);
            $user_detail = WorkshopMeta::where('workshop_id', $wid)->where('role', '!=', 0)->get();
            foreach ($user_detail as $key => $value) {
                if ($value->role == 1) {
                    $p_name = $value->user->fname . ' ' . $value->user->lname;
                    $p_email = $value->user->email;
                }
                if ($value->role == 2) {
                    $v_name = $value->user->fname . ' ' . $value->user->lname;
                    $v_email = $value->user->email;
                }
            }
            $meeting_data = Meeting::find($mid);
            if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                $pdfSeting = $workshop_data->setting['pdf'];
                $settings_data = json_decode(json_encode($pdfSeting));
            } else {
                $settings_data = getSettingData('pdf_graphic');
            }
            $WorkshopName = $workshop_data->workshop_name;
            $startDdate = dateConvert($meeting_data->date, 'd M Y');
            $meetingAddress = $meeting_data->place;
// $footer_center_text = str_replace(['[[WorkshopMeetingAddress]]', '[[WORKSHOP_NAME]]', '[[DATE]]'], [$meetingAddress, $WorkshopName, $startDdate], @$settings_data->footer_line1);
            $footer_center_text = str_replace(['[[WorkshopPresidentFullName]]', '[[WorkshopMeetingAddress]]', '[[WorkshopMeetingName]]', '[[WorkshopLongName]]', '[[WorkshopMeetingDate]]', '[[PresidentEmail]]', '[[WorkshopShortName]]', '[[WorkshopvalidatorFullName]]', '[[ValidatorEmail]]', '[[WorkshopShortName]]', '[[WorkshopMeetingTime]]', '[[MessageCategory]]', '[[PresidentPhone]]', '[[ProjectName]]'], [$p_name, $meetingAddress, $meeting_data->name, $WorkshopName, $startDdate, $p_email, $workshop_data->code1, $v_name, $v_email, $workshop_data->code1, '', '', '', ''], @$settings_data->footer_line1);
            $footer_line2 = @$settings_data->footer_line2;
            return view('pdf/repd_footer')->with(['footer_line1' => $footer_center_text, 'footer_line2' => $footer_line2]);
        }

        /*user redirect for adn.opsimplify.com account*/

        public function repdPdf($data)
        {

            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($data['wid']);
            $meeting_data = Meeting::find($data['mid']);
            if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                $pdfSeting = $workshop_data->setting['pdf'];
                $settings_data = json_decode(json_encode($pdfSeting));
            } else {
                $settings_data = getSettingData('pdf_graphic');

            }

            $WorkshopName = $workshop_data->workshop_name;
            $startDdate = dateConvert($meeting_data->date, 'd M Y');
            $incNumber = getIncrementNumber($data['wid']);
            $footer_center_text = str_replace(['[[WORKSHOP_NAME]]', '[[DATE]]'], [$WorkshopName, $startDdate], @$settings_data->footer_line1) . '' . @$settings_data->footer_line2;

            $file_date = date("d-m-Y", strtotime($meeting_data->date));
            $quotation = $this->Unaccent($workshop_data->code1) . "" . $this->Unaccent($workshop_data->code2) . "-" . date('y') . "" . (str_pad($incNumber, 3, "0", STR_PAD_LEFT));
            $pdf_name = str_replace(' ', '-', $quotation) . '-Releve-de-decisions-' . $this->Unaccent(str_replace(' ', '-', $WorkshopName)) . '-' . $file_date . '.pdf';
            $doc_title = $quotation . ' Releve de decisions ' . $WorkshopName . ' ' . $file_date;
            if (isset($data['version'])) {
                $pdf_name = str_replace(' ', '-', $quotation) . '-Releve-de-decisions-' . $this->Unaccent(str_replace(' ', '-', $WorkshopName)) . '-' . $file_date . $data['version'] . '.pdf';
                $doc_title = $quotation . ' Releve-de-decisions ' . $this->Unaccent(str_replace(' ', '-', $WorkshopName)) . ' ' . $file_date . $data['version'];
            }
            //this code must be there means below appending version
            $pdfUrl = public_path('public' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdf_name);


            $url = url('/') . "/gen-repd-pdf/" . $data['mid'] . "/" . $data['wid'].'/'.App::getLocale();
            $footer_url = url('/') . "/repd-footer/" . $data['mid'] . "/" . $data['wid'];
            $command = 'wkhtmltopdf --footer-font-size "9" --footer-html ' . $footer_url . ' --margin-bottom 36 --footer-spacing 10' . " $url $pdfUrl 2>&1";
//            dd($command);
            shell_exec($command);

            return ['pdf_name' => $pdf_name, 'title' => $doc_title, 'inc_number' => $incNumber];
        }


        public function incriptionPdf($data)
        {
            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($data['wid']);
            $meeting_data = Meeting::find($data['mid']);
            if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                $pdfSeting = $workshop_data->setting['pdf'];
                $settings_data = json_decode(json_encode($pdfSeting));
            } else {
                $settings_data = getSettingData('pdf_graphic');

            }
            $WorkshopName = $workshop_data->workshop_name;
            $startDdate = dateConvert($meeting_data->date, 'd M Y');
            $incNumber = getIncrementNumber($data['wid']);
            $footer_center_text = str_replace(['[[WORKSHOP_NAME]]', '[[DATE]]'], [$WorkshopName, $startDdate], @$settings_data->footer_line1) . '' . @$settings_data->footer_line2;
            $file_date = date("d-m-Y", strtotime($meeting_data->date));
            $quotation = $this->Unaccent($workshop_data->code1) . "" . $this->Unaccent($workshop_data->code2) . "-" . date('y') . "" . (str_pad($incNumber, 3, "0", STR_PAD_LEFT));
            $pdf_name = str_replace(' ', '-', $quotation) . '-Inscription-' . $this->Unaccent(str_replace(' ', '-', $WorkshopName)) . '-' . $file_date . '.pdf';
            $pdfUrl = public_path('public' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdf_name);
            $doc_title = $quotation . ' Inscription ' . $WorkshopName . ' ' . $file_date;
            $url = url('/') . "/inscription-pdf-view/" . $data['mid'] . "/" . $data['wid'].'/'.App::getLocale();
            $footer_url = url('/') . "/repd-footer/" . $data['mid'] . "/" . $data['wid'];
            $command = 'wkhtmltopdf --footer-font-size "9" --footer-html ' . $footer_url . ' --margin-bottom 26 --footer-spacing 10' . " $url $pdfUrl 2>&1";
            shell_exec($command);
            return ['pdf_name' => $pdf_name, 'title' => $doc_title, 'inc_number' => $incNumber];
        }

        /* ----------- End PDF Function ------------- */

        public function userRedirect()
        {
            return view('coming-soon');
        }

        public function __toString()
        {
            try {
                return (string)$this->name;
            } catch (Exception $exception) {
                return '';
            }
        }

        public function localToS3Upload($domain, $WorkshopName, $type, $file, $visibility = 'private')
        {
            $mountManager = new MountManager([
                's3'    => Storage::disk('s3')->getDriver(),
                'local' => Storage::disk('localPdf')->getDriver(),
            ]);
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            //$pdfUrl=public_path('public'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.$file);

            if (!Storage::disk('s3')->exists($domain . '/' . $WorkshopName . '/' . $type . '/' . $year . '/' . $month . '/' . $file)) {

                if (Storage::disk('localPdf')->exists($file)) {
                    $upload = ($mountManager->copy('local://' . $file, 's3://' . $domain . '/' . $WorkshopName . '/' . $type . '/' . $year . '/' . $month . '/' . $file, ['visibility' => $visibility]));
                    if ($upload) {
                        return $domain . '/' . $WorkshopName . '/' . $type . '/' . $year . '/' . $month . '/' . $file;
                    } else {
                        return NULL;
                    }
                } else {
                    return NULL;
                }
            } else {
                return $domain . '/' . $WorkshopName . '/' . $type . '/' . $year . '/' . $month . '/' . $file;
            }
        }

        public function getPrivateAsset($file, $time = 5)
        {
            return $url = Storage::disk('s3')->temporaryUrl(
                $file,
                now()->addMinutes($time)
            );
        }

        public function updateMilestoneStartDate()
        {
            $milestone = Milestone::get(['id', 'created_at']);
            $result = [];
            foreach ($milestone as $item) {
                if (!empty($item->created_at)) {
                    $time = strtotime($item->created_at);
                    $result[$item->id] = Milestone::where('id', $item->id)->whereNull('start_date')->update(['start_date' => date('Y-m-d', $time)]);
                } else {
                    $result[$item->id] = Milestone::where('id', $item->id)->whereNull('start_date')->update(['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]);
                }

            }
            echo 'total update record' . count($result);
            //dd($result);
        }

        public function updateTaskStatus()
        {
            $milestone = Task::get(['id', 'status']);
            $result = [];
            //Check status migration is run or run
            $checkStatus = DB::table('migrations')->where('migration', '2018_07_26_065934_create_activity_status_table')->count();
            if ($checkStatus == 0) {
                foreach ($milestone as $item) {
                    switch ($item->status) {
                        case 0:
                            $result[$item->id] = Task::where('id', $item->id)->update(['status' => (1)]);
                            break;
                        case 1:
                            $result[$item->id] = Task::where('id', $item->id)->update(['status' => (3)]);
                            break;
                        case 2:
                            $result[$item->id] = Task::where('id', $item->id)->update(['status' => (2)]);
                            break;
                    }
                }
                echo 'total update record' . count($result);
            } else {
                echo 'script already run';
            }

            //dd($result);
        }


        public function authorizeForMail()
        {
            $this->tenancy->website();
            $hostname = $this->tenancy->hostname();
//        $acc_id = 1;
            if (isset($hostname->id)) {
                $acc_id = $hostname->id;
                return DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first(['email_enabled']);

            } else {
                return 1;
            }
        }

        public function deleteS3Doc()
        {

        }

        public function getNewMemberAlert()
        {
            $this->tenancy->website();
            $hostname = $this->tenancy->hostname();
            // $acc_id = 1;
            if (isset($hostname->id)) {
                $acc_id = $hostname->id;
                return DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first(['new_member_alert']);
//
            } else {
                return 0;
            }
        }

        public function addOldWorkshopDefaultTask()
        {
            $project = [];
            $allworkshop = Workshop::all();
            $allProject = Project::where('is_default_project', 1)->get(['id', 'wid'])->pluck(['wid']);
            foreach ($allworkshop as $key => $value) {
                if (!in_array($value->id, $allProject->toArray())) {
                    $project = Project::create(['project_label'      => 'Projet Bazar',
                                                'user_id'            => $value->validator_id,
                                                'wid'                => $value->id,
                                                'color_id'           => 1,
                                                'is_default_project' => 1,
                                                'end_date'           => '2099-12-31 00:00:00',
                    ]);
                    //add default Entry in milestone Table
                    Milestone::create(['project_id'           => $project->id,
                                       'label'                => 'Étape Bazar',
                                       'user_id'              => $value->validator_id,
                                       'end_date'             => '2099-12-31 00:00:00',
                                       'color_id'             => 1,
                                       'start_date'           => Carbon::now(),
                                       'is_default_milestone' => 1,
                    ]);
                }
            }
//        dd($project);

        }

        function displayAlert(Request $request)
        {

            if (session()->has('message')) {
                list($type, $message) = explode('|', Session::get('message'));

                $type = $type ? 'error' : 'danger';
                $type = $type ? 'message' : 'info';

                return sprintf('<div class="alert alert-%s">%s</div>', $type, message);
            }

            return '';
        }

        public function appLink($id)
        {

            try {
                if (!empty($id)) {
                    if ($id == 2)
                        return redirect()->away(env('ANDROID_APP_LINK'));
                    elseif ($id == 1)
                        return redirect()->away(env('IOS_APP_LINK'));
                    else  throw new Exception('not found', 404);
                }
            } catch (Exception $e) {
                report($e);

                return FALSE;
            }

        }

        function testMailNew($id)
        {
            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($id);
            $mailData['mail'] = ['subject' => 'test', 'emails' => ['ravindra@sharabhtechnologies.com'], 'workshop_data' => $workshop_data, 'url' => ''];
            $this->SendMassEmail($mailData, 'candidate_commission_new_user');
        }

        public function launch()
        {
            // dd($_COOKIE);
            return view('launch.launch');
        }

        public function sendLaunchEmail(Request $request)
        {
            $data['mail'] = ['subject'   => 'New inforamtion request from OPSL',
                             'name'      => $request->name,
                             'emails'    => [
                                 'ravindra@sharabhtechnologies.com',
                                 'opbissa@sharabhtechnologies.com',
                                 // 'opslaunching@opsimplify.com'
                             ],
                             'formemail' => $request->email,
                             'op'        => $request->phone];
            $splitName = ['', ''];
            if (isset($_COOKIE['name'])) {
                $splitName = explode(' ', $_COOKIE['name']);
            }
            $data['mail']['referedEmail'] = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
            $data['mail']['referedFName'] = isset($_COOKIE['name']) ? $splitName[0] : '';
            $data['mail']['referedLName'] = isset($_COOKIE['name']) ? ((count($splitName) > 1) ? $splitName[1] : '') : '';

            $icontact = $this->saveInIcotactList($data['mail']);
            if ($icontact['status']) {
                $mail = $this->SendMassEmail($data, 'launching_email');
                if ($mail) {
                    return response()->json(['status' => TRUE, 'msg' => 'Email sent', 'data' => $request->all()], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'Email not sent', 'data' => $request->all()], 500);
                }
            } else {
                if ($icontact['case'] == 2) {
                    return response()->json(['status' => FALSE, 'msg' => 'email already exits', 'data' => $request->all()], 400);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'Somthing went wrong', 'data' => $request->all()], 500);
                }
            }
        }

        public function saveInIcotactList($data)
        {
            $splitName = explode(' ', $data['name']);
            $contact = [[
                'email'       => $data['formemail'],
                'firstName'   => $splitName[0],
                'lastName'    => (count($splitName) > 1) ? $splitName[1] : '',
                'business'    => $data['op'],
                'byemail'     => $data['referedEmail'],
                'byfirstname' => $data['referedFName'],
                'bylastname'  => $data['referedLName'],
            ]];
            $dataContact = $this->callIcontact('contacts', $contact);

            if (isset($dataContact->contacts) && count($dataContact->contacts) > 0) {
                $postData = [[
                    "listId"    => "51916",
                    "contactId" => $dataContact->contacts[0]->contactId,
                    "status"    => "normal",
                ]];
                $dataSub = $this->callIcontact('subscriptions', $postData);
                // dd($dataSub);
                if (isset($dataSub->subscriptions) && count($dataSub->subscriptions) > 0) {
                    return ['status' => TRUE, 'msg' => 'added', 'case' => 3];
                } else {
                    if (isset($dataSub->unchanged) && count($dataSub->unchanged) > 0) {
                        return ['status' => FALSE, 'msg' => 'email alreay exits', 'case' => 2];
                    } else {
                        return ['status' => FALSE, 'msg' => 'something went wrong!!', 'case' => 1];
                    }
                }
            } else {
                return ['status' => FALSE, 'msg' => 'something went wrong!!', 'case' => 1];
            }
        }

        public function callIcontact($url, $postdata)
        {
            $Iinstance = $this->getIContactInstance();
            $sResource = '/a/1693613/c/880/' . $url;
            return $Iinstance->makeCall($sResource, 'POST', $postdata);
        }

        public static function getIContactInstance()
        {
            iContactApi::getInstance()->setConfig([
                'appId'       => '0ee885d83e213b74e14ad2eb70468151',
                'apiPassword' => '4Gc361yEmxHMPIlCobReihzL',
                'apiUsername' => 'danake@internetbusinessbooster.com',
            ]);
            $oiContact = iContactApi::getInstance();
            return $oiContact;
        }

        public function createAPiKey($domain)
        {

            $validator = Validator::make(['domain' => $domain], [
                'domain' => 'required|exists:hostnames,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            if (session()->has('superadmin')) {
                $getHostname = Hostname::find($domain);
                $host = $this->tenancy->hostname($getHostname);
                $fileName = md5($host->id . '-' . $host->website_id);
                $combination = generateRandomString(10) . '-' . $host->id;
                $key = encrypt($combination);
                Storage::disk('tenant')->put($fileName . '.md', $key);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Unauthorized'], 422);
            }
        }

        public function getAPiKey($domain)
        {
            $getHostname = Hostname::find($domain);
            $host = $this->tenancy->hostname($getHostname);
            $fileName = md5($host->id . '-' . $host->website_id);
            if (Storage::disk('tenant')->exists($fileName . '.md')) {
                return Storage::disk('tenant')->get($fileName . '.md');
            } else {
                return NULL;
            }

        }

    }
