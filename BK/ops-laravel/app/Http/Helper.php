<?php

    use App\DocumentType;
    use App\Organisation;
    use App\RegularDocument;
    use App\Setting;
    use App\Signup;
    use App\SuperadminSetting;
    use App\LogHistory;
    use App\User;
    use App\Workshop;
    use Carbon\Carbon;
    use Hyn\Tenancy\Models\Hostname;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Auth;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\ReviewStep;
    use Modules\Qualification\Entities\Step;


    function getIncrementNumber($workshop_id)
    {
        $incNumber = 1;
        $res = RegularDocument::where('uncote', 0)->where('is_active', 1)->where('workshop_id', $workshop_id)->orderBy('increment_number', 'DESC')->first();

        if ($res) {
            // if (date('Y', strtotime($res->created_at)) < date('Y'))
            //     $incNumber = 1;
            // else
            $incNumber = $res->increment_number + 1;
        }
        return $incNumber;
    }

    function generateRandomValue($length = 10, $type = NULL, $characters="0123456789")
    {
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

    function getDocType($id)
    {
        $res = DocumentType::where('id', $id)->first();
        if ($res)
            return $res->document_code;
        return '';
    }

    function getOrgDetail()
    {
        $orgSetting = Organisation::first();
        $userDetail = User::where('role', 'M1')->first();
        $orgSetting['phone'] = ($userDetail != NULL) ? $userDetail->phone : '';
        return $orgSetting;
    }

// }function getOrgDetail() {
//     return Organisation::first();
// }

    function genRandomNum($length)
    {
        $string = '1928374656574839232764126534';
        $string_shuffled = str_shuffle($string);
        $otp = substr($string_shuffled, 1, $length);
        return $otp;
    }

    function generateRandomString($length = 10)
    {
        $characters = '123456789abcdefg9SHARABH2TECHNOLOGIES9hijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ9BHEEM2SWAMI';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function getDateYear($date)
    {
        return date('y', strtotime($date));
    }

    function chkUrlParams($key)
    {
        $exp = explode('-', $key);
        if ($key == '') {
            return 0;
        } else if ($exp[0] != 'security_token') {
            return 0;
        } else {
            return Signup::where('id', $exp[1])->where('code', $exp[2])->count();
        }
    }

    function timeConvert($time, $format = NULL)
    {
        if ($format == NULL)
            return date('H:i:s', strtotime($time));
        else
            return date($format, strtotime($time));
    }

    function dateConvertpdf($date = NULL, $format = NULL)
    {
        if ($date == NULL)
            return date($format);
        if ($format == NULL)
            return date('Y-m-d', strtotime($date));
        else
            return date($format, strtotime($date));
    }

    function dateConvert($date = NULL, $format = NULL, $userLang = '')
    {
        if (empty($userLang)) {
            if (isset(Auth::user()->setting) && !empty(Auth::user()->setting)) {
                $lang = json_decode(Auth::user()->setting);
            } else {
                $lang = session()->has('lang') ? session()->get('lang') : "FR";
            }
        } else
            $lang = $userLang;
        if ($lang == "EN") {
            setlocale(LC_ALL, 'en_IN');
            if ($date == NULL)
                return date($format);
            if ($format == NULL)
                return date('Y-m-d', strtotime($date));
            else
                return date($format, strtotime($date));
        } else {
            if ($date == NULL) {
                return date($format);
            }
            if ($format == NULL) {
                return date('Y-m-d', strtotime($date));
            } else {
                $dates = strftime('%A %d/%m/%Y', strtotime($date));

                $date_fr = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'samedi', 'dimanche'];
                $dateEn = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                return str_replace($dateEn, $date_fr, $dates);
            }
        }

    }

    function getWorkshopSettingData($wid)
    {
        $workshop = Workshop::withoutGlobalScopes()->find($wid);
        return $workshop->setting;
    }

    /*function getSettingData($key, $super_admin_key = 0) {
        if ($super_admin_key == 1) {
            $data = SuperadminSetting::where('setting_key', $key)->first();

        } else {
            $data = Setting::where('setting_key', $key)->first();
        }

        if ($data)
            return json_decode($data->setting_value);
        else
            return json_decode($data);
    }*/


    function getWorkshopSignatoryData($wid)
    {
        $workshop = Workshop::withoutGlobalScopes()->whereIn('is_qualification_workshop', [1, 2])->find($wid);
        if ($workshop != NULL) {
            return $workshop->signatory;
        } else {
            return [];
        }
    }

    function getSettingData($key, $super_admin_key = 0, $userLang = '')
    {
        if ($super_admin_key == 1) {
            $data = SuperadminSetting::where('setting_key', $key)->first();

        } else {
            if (empty($userLang)) {
                if (isset(Auth::user()->setting) && !empty(Auth::user()->setting)) {
                    $lang = json_decode(Auth::user()->setting);
                    @$lang = $lang->lang;
                } else {
                    $lang = session()->has('lang') ? session()->get('lang') : "FR";
                }
            } else
                $lang = $userLang;


            $allow = ['msg_push_setting', 'message_reply_push_setting', 'personal_message_push_setting', 'save_metting_push_setting', 'save_modify_metting_push_setting', 'agenda_push_setting', 'doodle_push_setting', 'doodle_reminder_push_setting', 'doodle_final_push_setting', 'decision_push_setting', 'alert_new_member_email'];

            if ($lang == 'EN' && in_array($key, $allow)) {
                $key = $key . '_EN';
            }

            $data = Setting::where('setting_key', $key)->first();
        }

        if ($data) {

            return json_decode($data->setting_value);
        } else
            return json_decode($data);


    }

    function emailCharactorEncode($text)
    {
        return '=?UTF-8?Q?' . quoted_printable_encode($text) . '?=';
    }

    function getEmailSetting($key)
    {
        $ctlr = app(\App\Http\Controllers\CoreController::class);
        $vals = [];
        $data = Setting::whereIn('setting_key', $key)->get();
        foreach ($data as $val) {
            $json_decode = json_decode($val->setting_value);
            if (in_array('email_graphic', $key)) {
                if ($json_decode->top_banner != '') {
                    $json_decode->top_banner = $ctlr->getS3Parameter($json_decode->top_banner, 2);
                }
                if ($json_decode->bottom_banner != '') {
                    $json_decode->bottom_banner = $ctlr->getS3Parameter($json_decode->bottom_banner, 2);
                }
                unset($key[array_search('email_graphic', $key)]);
            } else if (in_array('doodle_email_setting', $key)) {
                unset($key[array_search('doodle_email_setting', $key)]);
            }
            $vals[] = $json_decode;
        }
        return $vals;
    }

    function getWorkShopEmailSetting($key)
    {
        $ctlr = app(\App\Http\Controllers\CoreController::class);
        $vals = [];
        $data = Setting::whereIn('setting_key', $key)->get();
        foreach ($data as $val) {
            $json_decode = json_decode($val->setting_value);
            if (in_array('email_graphic', $key)) {
                if ($json_decode->top_banner != '') {
                    $json_decode->top_banner = $ctlr->getS3Parameter($json_decode->top_banner, 2);
                }
                if ($json_decode->bottom_banner != '') {
                    $json_decode->bottom_banner = $ctlr->getS3Parameter($json_decode->bottom_banner, 2);
                }
                unset($key[array_search('email_graphic', $key)]);
            } else if (in_array('doodle_email_setting', $key)) {
                unset($key[array_search('doodle_email_setting', $key)]);
            }
            $vals[] = $json_decode;
        }
        return $vals;
    }

    function getUserInfoByEmail($email)
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

    function dynamicCss()
    {
        $newData = [];
        $json_decode = getSettingData('graphic_config', 1);
        // dd($json_decode);
        if ($json_decode->header_logo != '') {
            $json_decode->header_logo = app(\App\Http\Controllers\CoreController::class)->getS3Parameter($json_decode->header_logo, 2);
            $newData['header_logo'] = $json_decode->header_logo;
        }
        if ($json_decode->right_header_icon != '') {
            $json_decode->right_header_icon = app(\App\Http\Controllers\CoreController::class)->getS3Parameter($json_decode->right_header_icon, 2);
            $newData['right_header_icon'] = $json_decode->right_header_icon;
        }

        $newData['color1'] = $json_decode->color1;
        $newData['transprancy7'] = $json_decode->color2;
        $newData['color2'] = $json_decode->color2;
        $newData['headerColor1'] = @$json_decode->headerColor1;
        $newData['headerColor2'] = @$json_decode->headerColor2;
        $newData['color3'] = '#0771b7';
        $newData['transprancy1'] = @$json_decode->transprancy1;
        $newData['transprancy2'] = @$json_decode->transprancy2;

        if ($newData['color1']) {
            $newData['color1'] = rgb2html($newData['color1']->r, $newData['color1']->g, $newData['color1']->b);
        } else {
            $newData['color1'] = '#0a8fc0';
        }


        if(isset($newData['color2_transprancy65']) && $newData['color2_transprancy65']) {
            $newData['color2_transprancy65'] = 'rgba(' . $newData['color2_transprancy65']->r . ',' . $newData['color2_transprancy65']->g . ',' . $newData['color2_transprancy65']->b . ',' . 0.30 . ')';
        } else {
            $newData['color2_transprancy65'] = 'rgba(0, 106, 176, 0.65)';
        }
    
//        if(isset($newData['color2_transprancy65']) && $newData['color2_transprancy65']) {
//            $newData['color2_transprancy65'] = 'rgba(' . $newData['color2_transprancy65']->r . ',' . $newData['color2_transprancy65']->g . ',' . $newData['color2_transprancy65']->b . ',' . 0.30 . ')';
//        } else {
//            $newData['color2_transprancy65'] = 'rgba(0, 106, 176, 0.65)';
//        }
        
        
        if ($newData['headerColor1']) {
            $newData['headerColor1'] = rgb2html($newData['headerColor1']->r, $newData['headerColor1']->g, $newData['headerColor1']->b);
        } else {
            $newData['headerColor1'] = '#FFFFFF';
        }

        if ($newData['headerColor2']) {
            $newData['headerColor2'] = rgb2html($newData['headerColor2']->r, $newData['headerColor2']->g, $newData['headerColor2']->b);
        } else {
            $newData['headerColor2'] = '#000000';
        }


        if ($newData['transprancy7']) {
            $newData['transprancy7'] = 'rgba(' . $newData['transprancy2']->r . ',' . $newData['transprancy2']->g . ',' . $newData['transprancy2']->b . ',' . 0.30 . ')';
        } else {
            $newData['transprancy7'] = 'rgba(0, 106, 176, 0.7)';
        }
        if ($newData['color2']) {
            $newData['color2'] = rgb2html($newData['color2']->r, $newData['color2']->g, $newData['color2']->b);
            $newData['color3'] = rgb2html((($json_decode->color2->r) * 0.75), (($json_decode->color2->g) * 0.75), ($json_decode->color2->b * 0.75));
        } else {
            $newData['color2'] = '#006ab0';
            $newData['color3'] = '#0771b7';
        }
        if ($newData['transprancy1']) {
            $newData['transprancy1'] = 'rgba(' . $newData['transprancy1']->r . ',' . $newData['transprancy1']->g . ',' . $newData['transprancy1']->b . ',' . $newData['transprancy1']->a . ')';
        } else {
            $newData['transprancy1'] = '#54aaeb';
        }
        if ($newData['transprancy2']) {
            $newData['transprancy2'] = 'rgba(' . $newData['transprancy2']->r . ',' . $newData['transprancy2']->g . ',' . $newData['transprancy2']->b . ',' . $newData['transprancy2']->a . ')';
        } else {
            $newData['transprancy2'] = '#23487C';
        }

        return $newData;
    }

    function rgb2html($r, $g = -1, $b = -1)
    {
        if (is_array($r) && sizeof($r) == 3)
            list($r, $g, $b) = $r;

        $r = intval($r);
        $g = intval($g);
        $b = intval($b);

        $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
        $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
        $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));

        $color = (strlen($r) < 2 ? '0' : '') . $r;
        $color .= (strlen($g) < 2 ? '0' : '') . $g;
        $color .= (strlen($b) < 2 ? '0' : '') . $b;
        return '#' . $color;
    }

    function checkValSet($value)
    {
        return ($value) ? 1 : 0;
    }

    function workshopValidatorPresident($workshop_data)
    {
        $member['p'] = ['fname' => '', 'lname' => '', 'fullname' => '', 'email' => '', 'phone' => ''];
        $member['v'] = ['fname' => '', 'lname' => '', 'fullname' => '', 'email' => '', 'phone' => ''];
        /*  if ($workshop_data->meta) {
              foreach ($workshop_data->meta as $k => $val) {
                  if ($val->role == 1) {
                      $member['p'] = ['fname' => utf8_decode($val->user->fname), 'lname' => utf8_decode($val->user->lname), 'fullname' => utf8_decode($val->user->fname . ' ' . $val->user->lname), 'email' => $val->user->email, 'phone' => $val->user->phone];
                  }
                  if ($val->role == 2) {
                      $member['v'] = ['fname' => utf8_decode($val->user->fname), 'lname' => utf8_decode($val->user->lname), 'fullname' => utf8_decode($val->user->fname . ' ' . $val->user->lname), 'email' => $val->user->email, 'phone' => $val->user->phone];
                  }
              }
          }*/
        if ($workshop_data->meta) {
            foreach ($workshop_data->meta as $k => $val) {
                if ($val->role == 1) {
                    if ($val->user != NULL)
                        $member['p'] = ['fname' => ($val->user->fname), 'lname' => ($val->user->lname), 'fullname' => ($val->user->fname . ' ' . $val->user->lname), 'email' => $val->user->email, 'phone' => $val->user->phone];
                }
                if ($val->role == 2) {
                    if ($val->user != NULL)
                        $member['v'] = ['fname' => ($val->user->fname), 'lname' => ($val->user->lname), 'fullname' => ($val->user->fname . ' ' . $val->user->lname), 'email' => $val->user->email, 'phone' => $val->user->phone];
                }
            }
        }

        return $member;
    }

    if (!function_exists('mb_str_replace')) {
        function mb_str_replace($search, $replace, $subject, &$count = 0)
        {
            if (!is_array($subject)) {
                // Normalize $search and $replace so they are both arrays of the same length
                $searches = is_array($search) ? array_values($search) : [$search];
                $replacements = is_array($replace) ? array_values($replace) : [$replace];
                $replacements = array_pad($replacements, count($searches), '');
                foreach ($searches as $key => $search) {
                    $parts = mb_split(preg_quote($search), $subject);
                    $count += count($parts) - 1;
                    $subject = implode($replacements[$key], $parts);
                }
            } else {
                // Call mb_str_replace for each subject in array, recursively
                foreach ($subject as $key => $value) {
                    $subject[$key] = mb_str_replace($search, $replace, $value, $count);
                }
            }
            return $subject;
        }
    }

    function setPasscode($hostCode, $randCode)
    {

//    if(substr($hostCode,0,1)>=5){
//        $hostCode=generateRandomValue(1,5).substr($hostCode,1);  //var_dump($hostCode);exit;
//    }
        $userCode = substr($hostCode, 0, 2) . substr($randCode, 0, 3) . substr($hostCode, 2, 1) . substr($randCode, 3, 1) . substr($hostCode, 3, 1);
        //left shift
        $hashCode = $userCode << 1;
        return ['userCode' => $userCode, 'hashCode' => $hashCode];
    }

    function getPasscode($hostCode)
    {
        //right shift
        $userHash = $hostCode >> 1;
        //new working for login using code
        if (strlen($hostCode) > 6) {
            $mainHash = substr($userHash, 0, 2) . substr($userHash, 5, 1) . substr($userHash, 7, 1);
        } else
            $mainHash = substr($userHash, 0, 2) . substr($userHash, 5, 1) . substr($userHash, 7, 1);
        return ['mainHash' => $mainHash, 'userHash' => $userHash];
    }

    function getHostNameData()
    {
//        $this->tenancy->website();
//        $hostdata = $this->tenancy->hostname();
//        $domain = @explode('.' . env('HOST_SUFFIX'), $hostdata->fqdn)[0];
//        //$domain = config('constants.HOST_SUFFIX');
//        session('hostdata', ['subdomain' => $domain]);
//        return $this->tenancy->hostname();
    }

    function presentStatus($p)
    {
        //->presence_status
        if (is_object($p)) {
            $p->load('meeting');
            if ($p->meeting->meeting_type == 2 && $p->video_presence_status == 1) {
                return __('message.present');
            } else {
                if ($p->presence_status == 'p' || $p->presence_status == 'P') {
                    return  __('message.present');
                } elseif ($p->presence_status == 'AE' || $p->presence_status == 'ae') {
                    return  __('message.AE');
                } else {
                    return __('message.ANE');
                }
            }
        }
    }

    function hybridPresentStatus($p)
    {
        if (is_object($p)) {
            $p->load('meeting');
            if ($p->meeting->meeting_type == 3 && $p->video_presence_status == 1) {
                return __('message.present');
            } else {
                    if ($p->video_presence_status == 'p' || $p->video_presence_status == 'P') {
                        return __('message.present');
                    } elseif ($p->video_presence_status == 'AE' || $p->video_presence_status == 'ae') {
                        return __('message.AE');
                    }elseif ($p->meeting->meeting_type == 3 && $p->video_presence_status == 0) {
                        return __('message.ANE');
                    } else {
                        return __('message.ANE');
                    }
            }
        }
    }

    function getWorkshopMember($workshop_data, $role)
    {

        if ($workshop_data) {
            foreach ($workshop_data->meta as $val) {
                if ($val->role == $role) {
                    echo $val->user->fname . ' ' . $val->user->lname;
                }
            }
        }
    }

    function checkValidDate($date, $format = 'Y-m-d')
    {
        $date = substr($date, 0, 10);
//    $date=date('Y-m-d', strtotime($date));
        if (count((explode('/', $date))) > 2) {
            $format = 'd/m/Y';
        } else {
            $format = 'Y-m-d';
        }
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
        // if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
        //     return true;
        // } else {
        //     return false;
        // }
    }

    function checkValidTime($time)
    {
        if (preg_match('#^[01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $time)) {
            return TRUE;
        }
        return FALSE;
    }

    function unique_multidim_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    function removeTagsInsideEmailTags()
    {
        $m = '<span style="color:black">Bonjour,<br><br>Merci de bien vouloir r&eacute;server la date de la prochaine r&eacute;union [[</span><span style="color:black">WorkshopLongName</span><span style="color:black">]]</span>&nbsp;<span style="color:black">&nbsp;le&nbsp; [[</span><span style="color:black">WorkshopMeetingDate</span><span style="color:black">]]</span>&nbsp;<span style="color:black">&nbsp;&agrave; [[</span><span style="color:black">WorkshopMeetingTime</span><span style="color:black">]]</span><span style="color:black">&nbsp;.</span><br><br><span style="color:black">Elle aura lieu &agrave; l&#39;adresse suivante :<br>[[</span><span style="color:black">WorkshopMeetingAddress</span><span style="color:black">]]&nbsp;</span>&nbsp;<span style="color:black">.<br><br>Merci de confirmer votre pr&eacute;sence au comit&eacute; et au d&eacute;jeuner&nbsp;en cliquant sur le lien ci-dessous :</span>';
        $str = preg_replace_callback("/\[(.*?)\]/", function ($m) {
            return strip_tags($m[0]);
        }, $m);
        dd($m, $str);
    }

    function checkUnique()
    {
        $hashRand = generateRandomValue(3, 1);
        $checkCodeUnique = DB::connection('mysql')->table('hostname_codes')->get(['hash'])->toArray();
        if (!in_array($hashRand, $checkCodeUnique)) {
            return $hashRand;
        } else {
            checkUnique();
        }
    }

    function getConsultationFormatAttribute($value, $time = NULL)
    {
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        if ($lang == 'FR') {
            setlocale(LC_ALL, 'fr_FR');
            if (!empty($time)) {
                $date = Carbon::parse($value)->format('F d, Y H:i');
            } else {
                $date = Carbon::parse($value)->format('F d, Y');
            }

            $date_fr = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return str_replace($dateEn, $date_fr, $date);
        } else {
            if (!empty($time)) {
                return Carbon::parse($value)->format('F d, Y H:i');
            } else {
                return Carbon::parse($value)->format('F d, Y');
            }
        }
    }

    function getCreatedAtAttribute($value)
    {
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        if ($lang == 'FR') {
            setlocale(LC_TIME, 'fr_FR');
            $date = Carbon::parse($value)->format('d F Y');
            $date_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return str_replace($dateEn, $date_fr, $date);
        } else {
            return Carbon::parse($value)->format('d F Y');
        }
    }

    function getCardIncrementNumber($workshop_id)
    {
        $incNumber = 1;
        $res = CandidateCard::where('workshop_id', $workshop_id)->whereYear('date_of_validation', Carbon::now()->format('Y'))->count();
        if ($res) {
            $incNumber = $res + 1;
        }
        return $incNumber;
    }

    function getUserWorkshopRole($user, $wid)
    {
        $workshop = $user->load('userMeta');
        $meta = $workshop->userMeta;
        $key = array_search($wid, $meta->pluck('workshop_id')->toArray());
        if ($key !== FALSE) {
            if ($meta[$key]->role == 1 || $meta[$key]->role == 2)
                return 1;
            else
                return 0;
        } else {
            return FALSE;
        }
    }

    function userLang()
    {
        $lang = 'FR';
        if (isset(Auth::user()->setting) && !empty(Auth::user()->setting)) {
            $lang = json_decode(Auth::user()->setting);
            $lang = strtoupper($lang->lang);
        } else {
            $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
        }
        return $lang;
    }

    function getUserLang($id)
    {
        $lang = 'FR';
        $user = User::find($id);
        if (isset($user->setting) && !empty($user->setting)) {
            $lang = json_decode($user->setting);
            $lang = strtoupper($lang->lang);
        } else {
            $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
        }
        return $lang;
    }

    function getFirstStepId()
    {
        $step = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->first(['id', 'sort_order']);
        if (isset($step->id)) {
            return $step->id;
        } else {
            return 1;
        }
    }

    function getCandidateUser($id)
    {
        $user = User::with('userSkillCompany')->find($id);
        if ($user) {
            return $user;
        } else {
            return [];
        }
    }

    function getGrantedDomain(int $userId)
    {
        $step = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'name']);

        $createdAt = Carbon::today()->format('Y');
        $user = User::with('userCards')->find($userId, ['id']);
        $carCount = count($user->userCards);
        if ($carCount > 1) {
            //to get max count array value
            $carCountKey = $carCount - 1;
            if (isset($user->userCards[$carCountKey])) {
                $year = Carbon::parse($user->userCards[$carCountKey]->created_at)->addYear(1)->format('Y');
                $createdAt = $year;
            }
        }

        $stepReview = ReviewStep::where(['user_id' => $userId, 'opinion_by' => 1])->whereYear('saved_for', $createdAt)->orderBy('step_id')->get(['id', 'opinion', 'step_id', 'user_id']);
        $series = [];
        $step->slice(1)->map(function ($item, $key) use (&$series, $stepReview, $userId) {

            $check = $stepReview->where('step_id', $item->id)->first();
            if (isset($check->step_id) && $check->opinion == 0) {
                $series[] = $item->name;
            }
        });

        if (count($series) > 0)
            return implode(',', $series);
        else
            return '';
    }

    function getCompanyName($userId)
    {
        $step = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->first();
        $getAdminStepFields = Step::where('id', $step->id)->with(['fields' => function ($b) {
            $b->where('skills.skill_format_id', 6)->select('skills.id', 'skills.skill_tab_id', 'skills.name', 'skills.short_name', 'skills.is_valid', 'skills.is_mandatory', 'skills.skill_format_id', 'skills.is_unique', 'skills.sort_order', 'skills.is_conditional', 'skills.is_qualifying');
        }, 'fields.skillFormat'                                            => function ($a) {
            $a->select('id', 'name_en', 'name_fr');
        }, 'fields.userSkill'                                              => function ($q) use ($userId) {
            $q->where('field_id', $userId)->where('type', 'candidate');
        }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance'])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
        if (count($getAdminStepFields->fields) > 0) {
            foreach ($getAdminStepFields->fields as $key => $value) {
                if ($value->userSkill != NULL && $value->userSkill->text_input != NULL) {
                    return $value->userSkill->text_input;
                }
            }
        }
        return '';
    }

    function getDomainGraphicSetting($domain)
    {
        $tenancy = app(\Hyn\Tenancy\Environment::class);
        $getHostname = Hostname::where('fqdn', $domain)->first();
        if (!empty($getHostname)) {
            $host = $tenancy->hostname($getHostname);

            $newData = [];
            $org = Organisation::first();
            $newData['host_name'] = isset($org->acronym) ? $org->acronym : 'OP simplify';
            $data = SuperadminSetting::where('setting_key', 'graphic_config')->first();
            $json_decode = json_decode($data->setting_value);
            $newData['color1'] = $json_decode->color1;
            $newData['transprancy7'] = $json_decode->color2;
            $newData['color2'] = $json_decode->color2;
            // $newData['headerColor1'] = $json_decode->headerColor1;
            // $newData['headerColor2'] = $json_decode->headerColor2;
            $newData['headerColor1'] = '';
            $newData['headerColor2'] = '';
            $newData['color1_transprancy1'] = '';
            $newData['color2_transprancy25'] = '';
            $newData['color2_transprancy65'] = '';
            $newData['color3'] = '#0771b7';
            $newData['transprancy1'] = @ $json_decode->transprancy1;
            $newData['transprancy2'] = @$json_decode->transprancy2;

            if ($newData['color1']) {
                $newData['color1_transprancy1'] = 'rgba(' . $newData['color1']->r . ',' . $newData['color1']->g . ',' . $newData['color1']->b . ',' . 0.10 . ')';
                $newData['color1'] = rgb2html($newData['color1']->r, $newData['color1']->g, $newData['color1']->b);
            } else {
                $newData['color1'] = '#0a8fc0';
                $newData['color1_transprancy1'] = 'rgba(0, 106, 176, 0.1)';
            }


            if ($newData['headerColor1']) {
                $newData['headerColor1'] = rgb2html($newData['headerColor1']->r, $newData['headerColor1']->g, $newData['headerColor1']->b);
            } else {
                $newData['headerColor1'] = '#FFFFFF';
            }

            if ($newData['headerColor2']) {
                $newData['headerColor2'] = rgb2html($newData['headerColor2']->r, $newData['headerColor2']->g, $newData['headerColor2']->b);
            } else {
                $newData['headerColor2'] = '#000000';
            }


            if ($newData['transprancy7']) {
                $newData['transprancy7'] = 'rgba(' . $newData['transprancy2']->r . ',' . $newData['transprancy2']->g . ',' . $newData['transprancy2']->b . ',' . 0.30 . ')';
            } else {
                $newData['transprancy7'] = 'rgba(0, 106, 176, 0.7)';
            }
            if ($newData['color2']) {
                $newData['color2_transprancy25'] = 'rgba(' . $newData['color2']->r . ',' . $newData['color2']->g . ',' . $newData['color2']->b . ',' . 0.25 . ')';
                $newData['color2_transprancy65'] = 'rgba(' . $newData['color2']->r . ',' . $newData['color2']->g . ',' . $newData['color2']->b . ',' . 0.65 . ')';
                $newData['color2'] = rgb2html($newData['color2']->r, $newData['color2']->g, $newData['color2']->b);
                $newData['color3'] = rgb2html((($json_decode->color2->r) * 0.75), (($json_decode->color2->g) * 0.75), ($json_decode->color2->b * 0.75));
            } else {
                $newData['color2_transprancy25'] = 'rgba(0, 106, 176, 0.75)';
                $newData['color2_transprancy65'] = 'rgba(0, 106, 176, 0.65)';
                $newData['color2'] = '#006ab0';
                $newData['color3'] = '#0771b7';
            }
            if ($newData['transprancy1']) {
                $newData['transprancy1'] = 'rgba(' . $newData['transprancy1']->r . ',' . $newData['transprancy1']->g . ',' . $newData['transprancy1']->b . ',' . $newData['transprancy1']->a . ')';
            } else {
                $newData['transprancy1'] = '#54aaeb';
            }
            if ($newData['transprancy2']) {
                $newData['transprancy2'] = 'rgba(' . $newData['transprancy2']->r . ',' . $newData['transprancy2']->g . ',' . $newData['transprancy2']->b . ',' . $newData['transprancy2']->a . ')';
            } else {
                $newData['transprancy2'] = '#23487C';
            }
            if ($json_decode->header_logo != '') {
                $json_decode->header_logo = app(\App\Http\Controllers\CoreController::class)->getS3Parameter($json_decode->header_logo, 2);
                $newData['header_logo'] = $json_decode->header_logo;
            }
            if ($json_decode->right_header_icon != '') {
                $json_decode->right_header_icon = app(\App\Http\Controllers\CoreController::class)->getS3Parameter($json_decode->right_header_icon, 2);
                $newData['right_header_icon'] = $json_decode->right_header_icon;
            }

            return $newData;
        } else {
            return dynamicCss();
        }
    }

    function getLabel($id)
    {
        $tenancy = app(\Hyn\Tenancy\Environment::class);
        //$explode=explode('.', $_SERVER['HTTP_HOST']);
        //if (count($explode) > 2 && in_array('com',$explode)) {
        if (!empty($tenancy->hostname())) {
            $label = \App\Model\LabelCustomization::where('id', $id)->first();
        }
        if (isset($label->id)) {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }
            $locale = App::getLocale();
            if ($label->on_off)
                $field = 'custom_' . strtolower($locale);
            else
                $field = 'default_' . strtolower($locale);
            if ($label->$field) {
                return ucfirst($label->$field);
            } else {
                $field = 'default_' . "$locale";
                return ucfirst($label->$field);
            }
        } else {
            return 'Bienvenue to OpSimplify';
        }
    }

    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function calculateTextBox($text, $fontFile, $fontSize, $fontAngle)
    {
        /************
         * simple function that calculates the *exact* bounding box (single pixel precision).
         * The function returns an associative array with these keys:
         * left, top:  coordinates you will pass to imagettftext
         * width, height: dimension of the image you have to create
         *************/
        $rect = imagettfbbox($fontSize, $fontAngle, $fontFile, $text);
        $minX = min([$rect[0], $rect[2], $rect[4], $rect[6]]);
        $maxX = max([$rect[0], $rect[2], $rect[4], $rect[6]]);
        $minY = min([$rect[1], $rect[3], $rect[5], $rect[7]]);
        $maxY = max([$rect[1], $rect[3], $rect[5], $rect[7]]);

        return [
            "left"   => abs($minX) - 1,
            "top"    => abs($minY) - 1,
            "width"  => $maxX - $minX,
            "height" => $maxY - $minY,
            "box"    => $rect,
        ];
    }

    function getQualificationCompanyName($user)
    {
        $company = '';
        if (isset($user->userSkillCompany->text_input)) {

            $str = $user->userSkillCompany->text_input;
            $exp = explode(' ', $str);
            if (strlen($exp[0]) > 18) {
                $trimstring = substr($str, 0, 18) . '...';
                $company = $trimstring;
            } else {
                $company = $exp[0];
            }
        }
        return $company;
    }

    function createImage()
    {
        $image_file = imagecreatefrompng('G:\work\ops-laravel/public/qualification/images/card-icon.png');
        $corner_radius = isset($_GET['radius']) ? $_GET['radius'] : 20; // The default corner radius is set to 20px
        $angle = isset($_GET['angle']) ? $_GET['angle'] : 0; // The default angle is set to 0º
        $topleft = (isset($_GET['topleft']) and $_GET['topleft'] == "no") ? FALSE : TRUE; // Top-left rounded corner is shown by default
        $bottomleft = (isset($_GET['bottomleft']) and $_GET['bottomleft'] == "no") ? FALSE : TRUE; // Bottom-left rounded corner is shown by default
        $bottomright = (isset($_GET['bottomright']) and $_GET['bottomright'] == "no") ? FALSE : TRUE; // Bottom-right rounded corner is shown by default
        $topright = (isset($_GET['topright']) and $_GET['topright'] == "no") ? FALSE : TRUE; // Top-right rounded corner is shown by default

        $images_dir = 'images/';
        $corner_source = imagecreatefrompng('images/rounded_corner.png');

        $corner_width = imagesx($corner_source);
        $corner_height = imagesy($corner_source);
        $corner_resized = ImageCreateTrueColor($corner_radius, $corner_radius);
        ImageCopyResampled($corner_resized, $corner_source, 0, 0, 0, 0, $corner_radius, $corner_radius, $corner_width, $corner_height);

        $corner_width = imagesx($corner_resized);
        $corner_height = imagesy($corner_resized);
        $image = imagecreatetruecolor($corner_width, $corner_height);
        $image = imagecreatefromjpeg($images_dir . $image_file); // replace filename with $_GET['src']
        $size = getimagesize($images_dir . $image_file); // replace filename with $_GET['src']
        $white = ImageColorAllocate($image, 255, 255, 255);
        $black = ImageColorAllocate($image, 0, 0, 0);

// Top-left corner
        if ($topleft == TRUE) {
            $dest_x = 0;
            $dest_y = 0;
            imagecolortransparent($corner_resized, $black);
            imagecopymerge($image, $corner_resized, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
        }

// Bottom-left corner
        if ($bottomleft == TRUE) {
            $dest_x = 0;
            $dest_y = $size[1] - $corner_height;
            $rotated = imagerotate($corner_resized, 90, 0);
            imagecolortransparent($rotated, $black);
            imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
        }

// Bottom-right corner
        if ($bottomright == TRUE) {
            $dest_x = $size[0] - $corner_width;
            $dest_y = $size[1] - $corner_height;
            $rotated = imagerotate($corner_resized, 180, 0);
            imagecolortransparent($rotated, $black);
            imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
        }

// Top-right corner
        if ($topright == TRUE) {
            $dest_x = $size[0] - $corner_width;
            $dest_y = 0;
            $rotated = imagerotate($corner_resized, 270, 0);
            imagecolortransparent($rotated, $black);
            imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
        }

// Rotate image
        $image = imagerotate($image, $angle, $white);

// Output final image
        imagejpeg($image);

// Remove temp files
        imagedestroy($image);
        imagedestroy($corner_source);
    }

    function imageCreateCorners($sourceImageFile, $radius = 10)
    {
        # test source image
        if (file_exists($sourceImageFile)) {
            $res = is_array($info = getimagesize($sourceImageFile));
        } else $res = FALSE;

        # open image
        if ($res) {
            $w = $info[0];
            $h = $info[1];

            switch ($info['mime']) {
                case 'image/jpeg':
                    $src = imagecreatefromjpeg($sourceImageFile);
                    break;
                case 'image/gif':
                    $src = imagecreatefromgif($sourceImageFile);
                    break;
                case 'image/png':
                    $src = imagecreatefrompng($sourceImageFile);
                    break;
                default:
                    $res = FALSE;
            }
        }

        # create corners
        if ($res) {

            $q = 8; # change this if you want
            $radius *= $q;

            # find unique color
            do {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
            } while (imagecolorexact($src, $r, $g, $b) < 0);

            $nw = $w * $q;
            $nh = $h * $q;

            $img = imagecreatetruecolor($nw, $nh);
            $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
            imagealphablending($img, FALSE);
            imagesavealpha($img, TRUE);
            imagefilledrectangle($img, 0, 0, $nw, $nh, $alphacolor);

            imagefill($img, 0, 0, $alphacolor);
            imagecopyresampled($img, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
            imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
            imagefilltoborder($img, $nw - 1, 0, $alphacolor, $alphacolor);
            imagearc($img, $radius - 1, $nh - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
            imagefilltoborder($img, 0, $nh - 1, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $nh - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
            imagefilltoborder($img, $nw - 1, $nh - 1, $alphacolor, $alphacolor);
            imagealphablending($img, TRUE);
            imagecolortransparent($img, $alphacolor);

            # resize image down
            $dest = imagecreatetruecolor($w, $h);
            imagealphablending($dest, FALSE);
            imagesavealpha($dest, TRUE);
            imagefilledrectangle($dest, 0, 0, $w, $h, $alphacolor);
            imagecopyresampled($dest, $img, 0, 0, 0, 0, $w, $h, $nw, $nh);

            # output image
            $res = $dest;
            imagedestroy($src);
            imagedestroy($img);
        }

        return $res;
    }

    function imageBox()
    {
        $im = imagecreatetruecolor(300, 150);
        $black = imagecolorallocate($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);

// Set the background to be white
        imagefilledrectangle($im, 0, 0, 299, 299, $white);

// Path to our font file
        $font = public_path() . 'public/OpenSans-Regular.ttf';

// First we create our bounding box for the first text
        $bbox = imagettfbbox(10, 0, $font, 'Powered by PHP ' . phpversion());

// This is our cordinates for X and Y
        $x = $bbox[0] + (imagesx($im) / 2) - ($bbox[4] / 2) - 25;
        $y = $bbox[1] + (imagesy($im) / 2) - ($bbox[5] / 2) - 5;

// Write it
        imagettftext($im, 10, 0, $x, $y, $black, $font, 'Powered by PHP ' . phpversion());

// Create the next bounding box for the second text
        $bbox = imagettfbbox(10, 0, $font, 'and Zend Engine ' . zend_version());

// Set the cordinates so its next to the first text
        $x = $bbox[0] + (imagesx($im) / 2) - ($bbox[4] / 2) + 10;
        $y = $bbox[1] + (imagesy($im) / 2) - ($bbox[5] / 2) - 5;

// Write it
        imagettftext($im, 10, 0, $x, $y, $black, $font, 'and Zend Engine ' . zend_version());

// Output to browser
        header('Content-Type: image/png');

        dd(imagepng($im));
    }

    function Unaccent($string)
    {
        $unwanted_array = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', '÷' => '', 'ü' => 'u', '\'' => '', '’' => '', '‘' => '', '?' => '', '!' => '', '*' => '-', ':' => '-', '/' => '_', '\\' => '_', '[' => '', ']' => ''];
        $str = strtr($string, $unwanted_array);

        return $str;
    }