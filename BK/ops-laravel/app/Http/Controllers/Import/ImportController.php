<?php

namespace App\Http\Controllers\Import;

use App\Entity;
use App\EntityUser;
use App\Http\Controllers\Controller;
use App\Industry;
use App\Issuer;
use App\Meeting;
use App\MeetingDocument;
use App\MessageCategory;
use App\Milestone;
use App\Model\WorkshopMetaTemp;
use App\Presence;
use App\Project;
use App\RegularDocument;
use App\Task;
use App\Tempusers;
use App\Topic;
use App\Union;
use App\User;
use App\Workshop;
use App\WorkshopCode;
use App\WorkshopMeta;
use Auth;
use Carbon;
use DB;
use Excel;
use File;
use Hash;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\MountManager;
use Validator;
use Zip;

class ImportController extends Controller
{
    private $core, $tenancy, $meeting;
    private $pattern = '/^[0-9\s +-]*$/';

    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->meeting = app(\App\Http\Controllers\MeetingController::class);
    }

    public function importIndustriesFamily(Request $request)
    {
        // return response()->json($request->all());
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData->toArray();
            $totalrecord = count($records);
            $error = $this->validationExcel($records);
            if (!$this->wrongFileValidation($records[0], 'family')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }
            $errorField = $error['errorField'];
            foreach ($records as $k => $val) {
                if (isset($errorField[$k]) == false) {
                    $family = Industry::where('name', $val['family'])->where('parent', null)->first(['id', 'name']);
                    if ($family) {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'Family already Exits'];
                    } else {
                        $scuess[$count] = Industry::create([
                            'name' => $val['family'],
                        ]);
                        $count++;
                    }
                }
            }
            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }

        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function importIndustries(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData->toArray();
            $totalrecord = count($records);
            $error = $this->validationExcel($records);
            if (!$this->wrongFileValidation($records[0], 'industry')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
                // return response()->json(['error'=>$errors,'status'=>201],201);
            }
            $errorField = $error['errorField'];

            foreach ($records as $k => $val) {
                if (isset($errorField[$k]) == false) {
                    $val = array_filter($val, function ($value) {
                        return $value !== null;
                    });

                    $Industry = Industry::where('name', $val['family'])->where('parent', null)->first(['id', 'name']);

                    if ($Industry) {

                        $data = ['name' => $val['industry'], 'parent' => $Industry->id];
                        $indcheck = Industry::where($data)->first(['id']);

                        if ($indcheck) {
                            $errors[] = ['line' => ($k + 2), 'msg' => 'Industry Already exits'];
                        } else {
                            $scuess[$count] = Industry::create($data);
                            $scuess[$count]['parent'] = $Industry->name;
                            $count++;
                        }
                    } else {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'Industry family not exits'];
                    }

                }
            }
            // var_dump($errors);exit;
            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }
        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function importUnion(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {

        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData->toArray();
            $totalrecord = count($records);
            $error = $this->validationExcel($records, 'union');
            if (!$this->wrongFileValidation($records[0], 'union')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }
            $errorField = $error['errorField'];
            $validRecord = [];

            foreach ($records as $k => $val) {
                if (isset($errorField[$k]) == false) {
                    $check = 1;

                    if (strlen(trim($val['contact_email'])) > 0) {
                        $validator = Validator::make($val, [
                            'contact_email' => 'email',
                        ]);
                        if ($validator->fails()) {
                            $check = 0;
                            $errors[] = ['line' => ($k + 2), 'msg' => $validator->errors()->first()];
                        }
                    }

                    if (strlen(trim($val['union_zipcode'])) > 0) {
                        if (!is_numeric(trim($val['union_zipcode']))) {
                            $check = 0;
                            $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid union_zipcode'];
                        }
                    }
                    if (strlen(trim($val['union_phone'])) > 0) {
                        if (!preg_match($this->pattern, trim($val['union_phone']))) {
                            $check = 0;
                            $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid union_phone'];
                        }
                    }
                    if (strlen(trim($val['union_fax'])) > 0) {
                        if (!preg_match($this->pattern, trim($val['union_fax']))) {
                            $check = 0;
                            $errors[] = ['line' => ($k + 2), 'msg' => 'union_fax must be in numeric form'];
                        }
                    }

                    if (strlen(trim($val['visible_in_directory'])) > 0) {
                        if (!is_numeric($val['visible_in_directory']) || $val['visible_in_directory'] > 1 || $val['visible_in_directory'] < 0) {
                            $check = 0;
                            $errors[] = ['line' => ($k + 2), 'msg' => 'visible_in_directory must have 1 or 0 value'];
                        }
                    }
                    if ($check) {
                        $validRecord[$k] = $val;
                    }
                }

            }

            foreach ($validRecord as $k => $val) {

                if (isset($errorField[$k]) == false) {
                    $family = Industry::where('name', trim($val['family']))->where('parent', null)->first(['id', 'name']);
                    if ($family) {
                        $Industry = Industry::where('name', trim($val['industry']))->where('parent', $family->id)->first(['id', 'name']);
                        if ($Industry) {
                            $union = Union::where('union_name', trim($val['union_name']))->count();
                            if ($union) {
                                $errors[] = ['line' => ($k + 2), 'msg' => 'Union already Exits'];
                            } else {
                                $unionType = (strlen(trim($val['visible_in_directory'])) > 0) ? $val['visible_in_directory'] : 1;
                                $is_internal = ($val['is_internal'] == 0) ? 0 : 1;
                                $scuess[$count] = Union::create(
                                    ['email' => $val['contact_email'],
                                        'family_id' => $family->id,
                                        'industry_id' => $Industry->id,
                                        'contact_button' => (isset($val['text_contact_button'])) ? $val['text_contact_button'] : 'Contact',
                                        'address1' => $val['union_address1'],
                                        'city' => $val['union_city'],
                                        'union_code' => $val['union_code'],
                                        'country' => ucfirst($val['union_country']),
                                        'union_name' => $val['union_name'],
                                        'telephone' => $val['union_phone'],
                                        'postal_code' => $val['union_zipcode'],
                                        'fax' => $val['union_fax'],
                                        'website' => $val['url'],
                                        'union_description' => empty($val['description']) ? '' : $val['description'],
                                        'logo' => empty($val['logo_file_name']) ? null : $val['logo_file_name'],
                                        'address2' => '',
                                        'union_type' => $unionType,
                                        'is_internal' => $is_internal
                                    ]);

                                $scuess[$count]['family_id'] = $family->name;
                                $scuess[$count]['industry_id'] = $Industry->name;
                                $count++;
                            }
                        } else {
                            $errors[] = ['line' => ($k + 2), 'msg' => 'Industry doesn`t exits'];
                        }
                    } else {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'Family doesn`t exits'];
                    }
                }
            }
            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }

        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function importUsers(Request $request)
    {

        $path = $request->file('file')->getRealPath();
        $excelData = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData->toArray();
            $totalrecord = count($records);
            $error = $this->validationExcel($records, 'users');
            if (!$this->wrongFileValidation($records[0], 'user')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }
            $errorField = $error['errorField'];
            $userRecord = collect($records);
            $validEmailRecord = collect();
            //check valid email


            $userRecord->map(function ($val, $k) use (&$errors, &$errorField, &$validEmailRecord) {
                if (isset($errorField[$k]) == false) {
                    $validator = Validator::make($val, [
                        'user_email' => 'email',
                    ]);
                    if (!$validator->fails()) {
                        $flag = 1;
                        if (strlen(trim($val['union_id'])) != strlen($val['union_id'])) {
                            if (!is_numeric($val['union_id'])) {
                                $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid union id "' . $val['union_id'] . '"'];
                                $flag = 0;
                            }
                        }
                        if ($flag) {
                            $val['excel_key'] = $k + 2;
                            $validEmailRecord->push($val);
                        }
                    } else {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid email address "' . $val['user_email'] . '"'];
                    }

                }
            });

            if ($validEmailRecord->count() > 0) {
                $existUnionId = Union::whereIn('id', $validEmailRecord->pluck('union_id'))->pluck('id')->toArray();
                $existUserEmail = User::whereIn('email', $validEmailRecord->pluck('user_email'))->pluck('email')->toArray();
                $existTempUserId = Tempusers::whereIn('email', $validEmailRecord->pluck('user_email'))->pluck('email')->toArray();
                $insertUserData = [];
                foreach ($validEmailRecord as $key => $value) {
                    $value['user_email'] = strtolower($value['user_email']);
                    if (!empty($value['union_id']) && !in_array($value['union_id'], $existUnionId)) {
                        $errors[] = ['line' => $value['excel_key'], 'msg' => 'Union id as ' . $value['union_id'] . ' not exist in system'];
                    } elseif (in_array($value['user_email'], $existUserEmail)) {
                        $errors[] = ['line' => $value['excel_key'], 'msg' => 'Email already Exists'];
                    } elseif (in_array($value['user_email'], $existTempUserId)) {
                        $errors[] = ['line' => $value['excel_key'], 'msg' => 'Email already Exists in imported list'];
                    } else {
                        $hostname = $this->getHostNameData();
                        // $hostCode = generateRandomValue(3);
                        $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                        $randCode = generateRandomValue(3);
                        // $newCode = setPasscode($hostCode, $randCode);
                        $newCode = setPasscode($hostCode->hash, $randCode);

                        $scuess[$count] = Tempusers::create([
                            'fname' => $value['user_firstname'],
                            'lname' => $value['user_lastname'],
                            'email' => $this->core->Unaccent(strtolower($value['user_email'])),
                            'password' => $this->core->Unaccent(Hash::make(strtolower($value['user_email']))),
                            'union_id' => (is_numeric($value['union_id'])) ? $value['union_id'] : null,
                            'function_union' => (!empty($value['position_in_union'])) ? $value['position_in_union'] : '',
                            'role' => 'M2',
                            'login_count' => 0,
                            'login_code' => $newCode['userCode'],
                            'hash_code' => $newCode['hashCode'],
                            'company' => (!empty($value['company'])) ? $value['company'] : '',
                            'position_in_company' => (!empty($value['position_in_company'])) ? $value['position_in_company'] : '',
                        ]);
                        $count++;
                    }
                }

            }
            // die;
            //Ravindra Code comment
            // foreach ($records as $k => $val) {
            //     if (isset($errorField[$k]) == false) {
            //         if (filter_var($val['useremail'], FILTER_VALIDATE_EMAIL)) {
            //             $user = User::where('email', $val['useremail'])->first();
            //             $tUSer = Tempusers::where('email', $val['useremail'])->first();
            //             if ($user) {
            //                 $errors[] = ['line' => ($k + 2), 'msg' => 'Email already Exists'];
            //             } elseif ($tUSer) {
            //                 $errors[] = ['line' => ($k + 2), 'msg' => 'Email already Exists in imported list'];
            //             } else {
            //                 $hostname = $this->getHostNameData();
            //                 $hostCode = generateRandomValue(3);
            //                 //$hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
            //                 $randCode = generateRandomValue(3);
            //                 $newCode = setPasscode($hostCode, $randCode);
            //                 // $newCode = setPasscode($hostCode->hash, $randCode);

            //                 $scuess[$count] = Tempusers::create([
            //                     'fname' => $val['userfn'],
            //                     'lname' => $val['userln'],
            //                     'email' => strtolower($val['useremail']),
            //                     'password' => Hash::make(strtolower($val['useremail'])),
            //                     'role' => 'M2',
            //                     'login_count' => 0,
            //                     'login_code' => $newCode['userCode'],
            //                     'hash_code' => $newCode['hashCode'],
            //                 ]);
            //                 $count++;
            //             }
            //         } else {
            //             $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid email address "' . $val['useremail'] . '"'];
            //         }
            //     }
            // }

            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }
        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function validationExcel($excel, $type = null)
    {

        $errors = [];
        $errorField = [];
        foreach ($excel as $key => $val) {

            foreach ($val as $k => $data) {

                if ($k != '0') {
                    switch ($type) {
                        case 'users':
                            if ($k == 'user_firstname' || $k == 'user_lastname' || $k == 'user_email') {
                                $this->checkEmpty($data, $key, $k, $errors, $errorField);
                            }
                            break;
                        case 'union':
                            if ($k == 'family' || $k == 'industry' || $k == 'union_name' || $k == 'union_code') {
                                $this->checkEmpty($data, $key, $k, $errors, $errorField);
                            }
                            break;
                        case 'past_meeting':

                            if ($k != 'description_of_meeting') {
                                $this->checkEmpty($data, $key, $k, $errors, $errorField);
                            }
                            break;
                        default:
                            $this->checkEmpty($data, $key, $k, $errors, $errorField);
                            break;
                    }

                }
            }
        }
        $data = ['errors' => $errors, 'errorField' => $errorField];

        return $data;
    }

    function checkEmpty($data, $key, $k, &$errors, &$errorField)
    {
        if (empty($data) && strlen($data) == 0) {
            $errors[] = ['line' => ($key + 2), 'msg' => $k . ' is empty'];
            $errorField[$key] = $k;
        } elseif (strlen($data) > 0 && strlen(trim($data)) == 0) {
            $errors[] = ['line' => ($key + 2), 'msg' => $k . ' is empty'];
            $errorField[$key] = $k;
        } else if ($data == null && strlen($data) == 0) {
            $errors[] = ['line' => ($key + 2), 'msg' => $k . ' is empty'];
            $errorField[$key] = $k;
        }

    }

    public function validationWorkshopExcel($excel)
    {
        $errors = [];
        $errorField = [];
        foreach ($excel as $key => $val) {

            foreach ($val as $k => $data) {
                if ($k != '0') {

                    if ($k != 'code2') {
                        if ($data != 0 && empty($data)) {
                            $errors[] = ['line' => ($key + 2), 'msg' => $k . ' is empty'];
                            $errorField[$key] = $k;
                        } elseif (strlen((string)$data) > 0 && strlen(trim((string)$data)) == 0) {
                            $errors[] = ['line' => ($key + 2), 'msg' => $k . ' is empty'];
                            $errorField[$key] = $k;
                        }
                    }
                }
            }
        }
        $data = ['errors' => $errors, 'errorField' => $errorField];
        return $data;
    }

    //temp user delete
    public function deleteTempUsers(Request $request)
    {
        $ids = explode(',', $request->ids);
        $delete = Tempusers::whereIn('id', $ids)->delete();
        if ($delete) {
            $temp = Tempusers::all();
            return response()->json(['msg' => 'delete scuessfull', 'ids' => $temp, 'status' => 200], 200);
        } else {
            return response()->json(['msg' => 'delete unscuessfull', 'status' => 201], 201);
        }

    }

    public function importTempUser(Request $request)
    {
        try {
            $ids = explode(',', $request->ids);
            $error = [];
            $result = [];
            $users = Tempusers::whereIn('id', $ids)->get();
            $userCollection = collect($users);
            $existEmail = User::whereIn('email', $userCollection->pluck('email'))->get();
            if (!empty($existEmail)) {
                foreach ($existEmail as $data) {
                    $error[] = ['email' => $data->email, 'msg' => 'Email already exits'];
                }
            }
            $newEmail = $userCollection->whereNotIn('email', $existEmail->pluck('email'));
            if ($newEmail->count() > 0) {
                $insertData = [];
                foreach ($newEmail as $data) {
                    $insertData = ['fname' => $data->fname,
                        'lname' => $data->lname,
                        'email' => $data->email,
                        'password' => Hash::make($data->email),
                        'role' => 'M2',
                        'login_count' => 0,
                        'login_code' => $data->login_code,
                        'hash_code' => $data->hash_code,
                        'created_at' => date('Y-m-d H:i:s'),
                        'import_email' => false,
                        'union_id' => $data->union_id,
                        'function_union' => $data->position_in_union
                    ];
                    $result = User::create($insertData);
                    if ($result) {

                        /*@todo this code can be optimized as done in emergency(SP)*/
                        $entity = Entity::where('long_name', trim($data->company))->first();
                        if (!isset($entity->email)) {
                            $entity = Entity::create(['long_name' => $data->company, 'short_name' => '', 'address1' => '', 'address2' => '', 'zip_code' => '', 'city' => '', 'country' => '', 'phone' => '', 'email' => $this->core->Unaccent(strtolower(trim($result->email))), 'entity_type_id' => 2]);

                            $entityUser = EntityUser::create(['user_id' => $result->id, 'entity_id' => $entity->id, 'entity_label' => $data->position_in_company]);

                        } else {
                            $entityUser = EntityUser::where(['user_id' => $result->id])->first();
                            if (isset($entityUser->entity_label)) {
                                $entityUser->entity_label = $data->position_in_company;
                                $entityUser->save();
                            } else {
                                $entityUser = EntityUser::create(['user_id' => $result->id, 'entity_id' => $entity->id, 'entity_label' => $data->position_in_company]);

                            }
                        }


                    }

                }
                if ($result) {
                    $deleteTempData = Tempusers::whereIn('id', $userCollection->whereNotIn('email', $existEmail->pluck('email'))->pluck('id'))->delete();

                    return response()->json(['msg' => 'User imports scuessfull', 'erros' => $existEmail, 'scuess' => $newEmail, 'status' => 200], 200);
                } else {
                    return response()->json(['msg' => 'User imports error', 'erros' => $error, 'status' => 202], 202);
                }
            } else {
                return response()->json(['msg' => 'User Import error', 'erros' => $error, 'status' => 202], 202);
            }
        } catch (\Exception $e) {
            return response()->json(['msg' => $e->getMessage()], 500);
        }
        /* Ravindra Code*/
        // if ($users) {
        //     foreach ($users as $key => $val) {
        //         if (User::where('email', $val['email'])->first()) {
        //             $error[] = ['email' => $val['email'], 'msg' => 'Email already exits'];
        //         } else {
        //             User::create([
        //                 'fname' => $val['fname'],
        //                 'lname' => $val['lname'],
        //                 'email' => $val['email'],
        //                 'role' => 'M2',
        //                 'login_count' => 0,
        //                 'login_code' =>  $val['email'],
        //                 'hash_code' => $val['email'],
        //             ]);
        //             $delete = Tempusers::where('id', $val['id'])->delete();

        //         }
        //     }
        //     if (count($erros) > 0) {
        //         return response()->json(['msg' => 'Email send unscuessfull', 'erros' => $erros, 'status' => 202], 202);
        //     } else {
        //         $temp = Tempusers::all();
        //         return response()->json(['msg' => 'Email send scuessfull', 'ids' => $temp, 'status' => 200], 200);
        //     }
        // } else {
        //     return response()->json(['msg' => 'Email send scuessfull', 'status' => 201], 201);
        // }

    }

    // end temp user

    public function ImportWorkshop(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::load($path, function ($reader) {

        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        if (!empty($excelData) && count($excelData) > 0) {

            $records = $excelData->toArray();
            $totalrecord = count($records);

            if (!$this->wrongFileValidation($records[0], 'workshop')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            $error = $this->validationWorkshopExcel($records);
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }

            $errorField = $error['errorField'];

            foreach ($records as $k => $val) {
                if (isset($errorField[$k]) == false) {
                    if (!preg_match("/^[a-zA-Z0-9&àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ '-]*$/ui", $val['workshopname'])) {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'workshopname' . ' is invalid'];

                    } else {
                        // if(isset($errorField[$k])==false){
                        $workshopType = '';
                        if ($val['code1'] != null && $val['code1'] != '' && $val['code2'] != null && $val['code2'] != '') {
                            $workshopType = 'group';
                        } else {
                            $workshopType = 'single';
                        }
                        if ($workshopType == 'single') {
                            $checkcode = Workshop::where('code1', $val['code1'])->first(['id']);
                            //changing this validation as client allow us to code can have length 2-5(SP)
                            if (!$checkcode && strlen((string)$val['code1']) >= 2 && strlen((string)$val['code1']) <= 5) {
                                $secretaryemail = User::where('email', $val['secretaryemail'])->first(['id', 'fname', 'lname']);
                                if ($secretaryemail) {
                                    $metaData['secretaryemail'] = [
                                        'user_id' => $secretaryemail->id,
                                        'role' => 1,
                                    ];
                                } else {
                                    $metaData = [];
                                    $secretaryemail = null;
                                    $errors[] = ['line' => ($k + 2), 'msg' => $val['secretaryemail'] . ' not available in system' . ' is invalid'];
                                    // $newCode = $this->generatePassCode();
                                    // $secretaryemail = User::create([
                                    //     'fname' => $val['secretaryfn'],
                                    //     'lname' => $val['secretaryln'],
                                    //     'email' => $val['secretaryemail'],
                                    //     'password' => Hash::make($val['secretaryemail']),
                                    //     'role' => 'M2',
                                    //     'role_commision' => 1,
                                    //     'login_code' => $newCode['userCode'] ?? '',
                                    //     'hash_code' => $newCode['hashCode'] ?? '',

                                    // ]);
                                    // $dataMail = $this->meeting->getUserMailData('user_email_setting');
                                    // $route = route(
                                    //     'redirect-meeting-view', [
                                    //         'userid' => base64_encode($secretaryemail->id),
                                    //         'type' => 'm',
                                    //         'url' => str_rot13('dashboard'),
                                    //     ]);
                                    // $subject = utf8_encode($dataMail['subject']);
                                    // $mailData['mail'] = ['subject' => $subject, 'email' => strtolower($secretaryemail->email), 'password' => strtolower($secretaryemail->email), 'url' => $route];
                                    // $this->core->SendEmail($mailData, 'new_user');

                                    // $metaData['secretaryemail'] = [
                                    //     'user_id' => $secretaryemail->id,
                                    //     'role' => 1,
                                    // ];

                                }
                                $deputyemail = User::where('email', $val['deputyemail'])->first(['id', 'fname', 'lname']);
                                if ($deputyemail) {
                                    $metaData['deputyemail'] = [
                                        'user_id' => $deputyemail->id,
                                        'role' => 2,
                                    ];
                                } else {
                                    $deputyemail = null;
                                    $metaData = [];
                                    $errors[] = ['line' => ($k + 2), 'msg' => $val['deputyemail'] . ' not available in system' . ' is invalid'];

                                    // $newCode = $this->generatePassCode();
                                    // $deputyemail = User::create([
                                    //     'fname' => $val['deputyfn'],
                                    //     'lname' => $val['deputyln'],
                                    //     'email' => $val['deputyemail'],
                                    //     'password' => Hash::make($val['deputyemail']),
                                    //     'role' => 'M2',
                                    //     'login_code' => $newCode['userCode'] ?? '',
                                    //     'hash_code' => $newCode['hashCode'] ?? '',
                                    // ]);
                                    // if ($deputyemail) {
                                    //     $route = route(
                                    //         'redirect-meeting-view', [
                                    //             'userid' => base64_encode($deputyemail->id),
                                    //             'type' => 'm',
                                    //             'url' => str_rot13('dashboard'),
                                    //         ]);
                                    //     $subject = utf8_encode($dataMail['subject']);
                                    //     $mailData['mail'] = ['subject' => $subject, 'email' => strtolower($deputyemail->email), 'password' => strtolower($deputyemail->email), 'url' => $route];
                                    //     $this->core->SendEmail($mailData, 'new_user');

                                    // }
                                    // $metaData['deputyemail'] = [
                                    //     'user_id' => $deputyemail->id,
                                    //     'role' => 2,
                                    // ];
                                }
                                if ($secretaryemail != null && $deputyemail != null) {
                                    $data = ['workshop_name' => $val['workshopname'],
                                        'workshop_desc' => $val['workshop_description'],
                                        'code1' => strtoupper($val['code1']),
                                        'code2' => strtoupper($val['code2']),
                                        'workshop_type' => 1,
                                        'is_private' => ($val['is_visible'] == 0) ? 1 : 0,
                                        'president_id' => $secretaryemail->id,
                                        'validator_id' => $deputyemail->id,
                                    ];

                                    $scuess[$count] = Workshop::create($data);
                                    $metaData['deputyemail']['workshop_id'] = $scuess[$count]->id;
                                    $metaData['secretaryemail']['workshop_id'] = $scuess[$count]->id;
                                    $meta = WorkshopMeta::create($metaData['deputyemail']);
                                    $meta = WorkshopMeta::create($metaData['secretaryemail']);
                                    //code commented due to need to send emails
                                    /* if ($meta) {
                                        $emails = [$secretaryemail->email, $deputyemail->email];
                                        $dataMail = $this->getMailData($scuess[$count], 'commission_new_user');
                                        $subject = $dataMail['subject'];
                                        $mailData['mail'] = ['subject' => ($subject), 'emails' => array_unique($emails), 'workshop_data' => $scuess[$count], 'url' => $dataMail['route_members']];

                                        * @todo

                                        $this->core->SendMassEmail($mailData, 'new_commission_user');
                                    }*/
                                    WorkshopCode::create(['workshop_id' => $scuess[$count]->id, 'code' => $val['code1']]);
                                    MessageCategory::create(['category_name' => 'General', 'workshop_id' => $scuess[$count]->id, 'status' => 1]);
                                    $scuess[$count]['president_id'] = $secretaryemail->fname . ' ' . $secretaryemail->lname;
                                    $scuess[$count]['validator_id'] = $deputyemail->fname . ' ' . $deputyemail->lname;
                                    $count++;
                                }
                            } else {
                                //changing this validation as client allow us to code can have length 2-5(SP)
                                if (strlen((string)$val['code1']) < 2 || strlen((string)$val['code1']) > 5) {
                                    $errors[] = ['line' => ($k + 2), 'msg' => 'code1 length must be between 2-5'];
                                } else {
                                    $errors[] = ['line' => ($k + 2), 'msg' => 'code1 already exits'];
                                }
                            }
                        } elseif ($workshopType == 'group') {
                            $checkcode = Workshop::where('code1', $val['code1'])->first(['id']);
                            //changing this validation as client allow us to code can have length 2-5(SP)
                            if ($checkcode && (strlen((string)$val['code1']) >= 2 && strlen((string)$val['code1']) <= 5) && (strlen((string)$val['code2']) >= 2 && strlen((string)$val['code2']) <= 5)) {
                                $checkcode2 = $this->checkValidCombination(collect($val));
                                if (!$checkcode2) {
                                    $secretaryemail = User::where('email', $val['secretaryemail'])->first(['id', 'fname', 'lname']);
                                    if ($secretaryemail) {
                                        $metaData['secretaryemail'] = [
                                            'user_id' => $secretaryemail->id,
                                            'role' => 1,
                                        ];
                                    } else {
                                        $metaData = [];
                                        $secretaryemail = null;
                                        $errors[] = ['line' => ($k + 2), 'msg' => $val['secretaryemail'] . ' not available in system' . ' is invalid'];
                                        // $newCode = $this->generatePassCode();
                                        // $secretaryemail = User::create([
                                        //     'fname' => $val['secretaryfn'],
                                        //     'lname' => $val['secretaryln'],
                                        //     'email' => $val['secretaryemail'],
                                        //     'password' => Hash::make($val['secretaryemail']),
                                        //     'role' => 'M2',
                                        //     'role_commision' => 1,
                                        //     'login_code' => $newCode['userCode'] ?? '',
                                        //     'hash_code' => $newCode['hashCode'] ?? '',

                                        // ]);
                                        // $dataMail = $this->meeting->getUserMailData('user_email_setting');
                                        // $route = route(
                                        //     'redirect-meeting-view', [
                                        //         'userid' => base64_encode($secretaryemail->id),
                                        //         'type' => 'm',
                                        //         'url' => str_rot13('dashboard'),
                                        //     ]);
                                        // $subject = utf8_encode($dataMail['subject']);
                                        // $mailData['mail'] = ['subject' => $subject, 'email' => strtolower($secretaryemail->email), 'password' => strtolower($secretaryemail->email), 'url' => $route];
                                        // $this->core->SendEmail($mailData, 'new_user');
                                    }
                                    $deputyemail = User::where('email', $val['deputyemail'])->first(['id', 'fname', 'lname']);
                                    if ($deputyemail) {
                                        $metaData['deputyemail'] = [
                                            'user_id' => $deputyemail->id,
                                            'role' => 2,
                                        ];
                                    } else {
                                        // $newCode = $this->generatePassCode();
                                        // $deputyemail = User::create([
                                        //     'fname' => $val['deputyfn'],
                                        //     'lname' => $val['deputyln'],
                                        //     'email' => $val['deputyemail'],
                                        //     'password' => Hash::make($val['deputyemail']),
                                        //     'role' => 'M2',
                                        //     'login_code' => $newCode['userCode'] ?? '',
                                        //     'hash_code' => $newCode['hashCode'] ?? '',
                                        // ]);
                                        // if ($deputyemail) {
                                        //     $route = route(
                                        //         'redirect-meeting-view', [
                                        //             'userid' => base64_encode($deputyemail->id),
                                        //             'type' => 'm',
                                        //             'url' => str_rot13('dashboard'),
                                        //         ]);
                                        //     $subject = utf8_encode($dataMail['subject']);
                                        //     $mailData['mail'] = ['subject' => $subject, 'email' => strtolower($deputyemail->email), 'password' => strtolower($deputyemail->email), 'url' => $route];
                                        //     $this->core->SendEmail($mailData, 'new_user');

                                        // }
                                        // $metaData['deputyemail'] = [
                                        //     'user_id' => $deputyemail->id,
                                        //     'role' => 2,
                                        // ];
                                        $deputyemail = null;
                                        $metaData = [];
                                        $errors[] = ['line' => ($k + 2), 'msg' => $val['deputyemail'] . ' not available in system' . ' is invalid'];
                                    }
                                    if ($secretaryemail != null && $deputyemail != null) {

                                        $data = ['workshop_name' => $val['workshopname'],
                                            'workshop_desc' => $val['workshop_description'],
                                            'code1' => strtoupper($val['code1']),
                                            'code2' => strtoupper($val['code2']),
                                            'workshop_type' => 2,
                                            'is_private' => ($val['is_visible'] == 0) ? 1 : 0,
                                            'president_id' => $secretaryemail->id,
                                            'validator_id' => $deputyemail->id,
                                        ];

                                        $scuess[$count] = Workshop::create($data);
                                        $metaData['deputyemail']['workshop_id'] = $scuess[$count]->id;
                                        $metaData['secretaryemail']['workshop_id'] = $scuess[$count]->id;
                                        $meta = WorkshopMeta::create($metaData['deputyemail']);
                                        $meta = WorkshopMeta::create($metaData['secretaryemail']);

                                        /* if ($meta) {
                                                               $emails = [$val['deputyemail'], $val['secretaryemail']];

                                                               $dataMail = $this->getMailData($scuess[$count], 'commission_new_user');

                                                               $subject = $dataMail['subject'];
                                                               $mailData['mail'] = ['subject' => ($subject), 'emails' => array_unique($emails), 'workshop_data' => $scuess[$count], 'url' => $dataMail['route_members']];

                                                           *@todo

                                                               $this->core->SendMassEmail($mailData, 'new_commission_user');
                                                           }
                                                           */
                                        MessageCategory::create(['category_name' => 'General', 'workshop_id' => $scuess[$count]->id, 'status' => 1]);
                                        $scuess[$count]['president_id'] = $secretaryemail->fname . ' ' . $secretaryemail->lname;
                                        $scuess[$count]['validator_id'] = $deputyemail->fname . ' ' . $deputyemail->lname;
                                        $count++;
                                    }
                                } else {
                                    $errors[] = ['line' => ($k + 2), 'msg' => 'code2 already exits'];
                                }
                            } else {
                                //changing this validation as client allow us to code can have length 2-5(SP)
                                if (strlen((string)$val['code1']) < 2 || strlen((string)$val['code1']) > 5) {
                                    $errors[] = ['line' => ($k + 2), 'msg' => 'code1 length must be between 2-5'];
                                } else if (strlen((string)$val['code2']) < 2 || strlen((string)$val['code2']) > 5) {
                                    $errors[] = ['line' => ($k + 2), 'msg' => 'code2 length must be between 2-5'];
                                } else {
                                    $errors[] = ['line' => ($k + 2), 'msg' => $val['code1'] . 'code1 not found'];

                                }
                            }

                        } else {
                            $errors[] = ['line' => ($k + 2), 'msg' => 'code1 and code2 are null'];
                        }
                    }
                }
            }

            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }

        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function importMember(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::load($path, function ($reader) {
        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        $newMemberEmail = [];
        $newUser = collect();
        $existUser = collect();
        $existMember = collect();
        $insertData = collect();
        $insertDataDuplicay = collect();
        $validRecord = collect();
        if (!empty($excelData) && count($excelData) > 0) {
            $workshop_data = Workshop::with('meta')->find($request->workshop_id);
            $records = $excelData->toArray();
            $totalrecord = count($records);
            $error = $this->validationExcel($records);
            if (!$this->wrongFileValidation($records[0], 'member')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }
            $errorField = $error['errorField'];
            $recordCollection = collect($records);


            //Filter record for invalid email
            $recordCollection->each(function ($item, $k) use ($errors, $validRecord, $errorField) {
                if (isset($errorField[$k]) == false) {
                    if (filter_var(strtolower($item['user_email']), FILTER_VALIDATE_EMAIL)) {
                        $validRecord->push($item);
                    } else {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid email address "' . $item['user_email'] . '"'];
                    }
                }
            });
            //check email already exist and new Users email
            $userTableExist = collect(User::whereIn('email', $validRecord->pluck('user_email')->toArray())->pluck('email', 'id'));
            // print_r($userTableExist->search('yash@sharabh.com'));die;
            $validRecord->each(function ($val) use ($userTableExist, $newUser, $existUser) {
                $email = strtolower($val['user_email']);
                if ($userTableExist->search($email)) {

                    $val['user_id'] = $userTableExist->search($email);
                    $existUser->push($val);
                } else {
                    $newUser->push($val);
                }
            });


            if ($newUser->count() > 0) {
                $recordCollection->each(function ($item, $k) use (&$errors, $newUser, $errorField) {
                    if (isset($errorField[$k]) == false) {

                        if ($newUser->where('user_email', $item['user_email'])->count()) {
                            $errors[] = ['line' => ($k + 2), 'msg' => 'email address  "' . $item['user_email'] . '" not exists in system'];
                        }
                    }
                });

                // $dataMail = $this->meeting->getUserMailData('user_email_setting');
                // $route = route(
                //     'redirect-meeting-view', [
                //         'userid' => base64_encode(0),
                //         'type' => 'm',
                //         'url' => str_rot13('dashboard'),
                //     ]);
                // $subject = utf8_encode($dataMail['subject']);

                // $newUser->each(function ($val) use ($request, $insertData, $route, $subject) {
                // $newCode = $this->generatePassCode();
                // $newUser = User::create(['fname' => $val['user_first_name'],
                //     'lname' => $val['user_last_name'],
                //     'email' => $val['user_email'],
                //     'password' => Hash::make($val['user_email']),
                //     'role' => 'M2',
                //     'login_count' => 0,
                //     'login_code' => $newCode['userCode'] ?? '',
                //     'hash_code' => $newCode['hashCode'] ?? '',
                //     'import_member_email' => 0,
                // ]);
                // if ($newUser) {
                //send Mail to new user
                // $mailData['mail'] = ['subject' => $subject, 'email' => strtolower($newUser->email), 'password' => strtolower($newUser->email), 'url' => $route];
                // $this->core->SendEmail($mailData, 'new_user');
                //Insert for workshop meta
                // }
                // });
            }
            //Check exist user in workshop member if found error of already exist
            if ($existUser->count() > 0) {
                $existMember = collect(WorkshopMeta::with('user:id,email')
                    ->where('workshop_id', $request->workshop_id)
                    ->whereIn('user_id', $existUser->pluck('user_id')->toArray())
                    ->groupBy('user_id')
                    ->get(['id', 'workshop_id', 'user_id']));
                $existTempMember = collect(WorkshopMetaTemp::with('user:id,email')
                    ->where('workshop_id', $request->workshop_id)
                    ->whereIn('user_id', $existUser->pluck('user_id')->toArray())
                    ->groupBy('user_id')
                    ->get(['id', 'workshop_id', 'user_id']));

                if ($existMember->count() > 0 || $existTempMember->count() > 0) {
                    //Show  error of already exist email
                    $recordCollection->each(function ($val, $k) use ($existMember, &$errors, $existTempMember) {
                        if (in_array(strtolower($val['user_email']), $existMember->pluck('user')->pluck('email')->toArray())) {
                            $errors[] = ['line' => ($k + 2), 'msg' => 'Email already exist in workshop"' . $val['user_email'] . '"'];
                        } else if (in_array(strtolower($val['user_email']), $existTempMember->pluck('user')->pluck('email')->toArray())) {
                            $errors[] = ['line' => ($k + 2), 'msg' => 'Email already exist in temp member list"' . $val['user_email'] . '"'];

                        }
                    });
                    $existEmail = $existMember->pluck('user')->pluck('email')->toArray();
                    $tempEmail = $existTempMember->pluck('user')->pluck('email')->toArray();
                    //Final insert data without existing user
                    $existUser->each(function ($val, $key) use ($existEmail, $existUser, $tempEmail) {
                        if (in_array(strtolower($val['user_email']), $existEmail)) {
                            $existUser->forget($key);
                        }
                        if (in_array(strtolower($val['user_email']), $tempEmail)) {
                            $existUser->forget($key);
                        }
                    });
                }
                //check temp list
                // if ( $existTempMember->count() > 0) {
                //     //Show  error of already exist email
                //     $recordCollection->each(function ($val, $k) use ( $existTempMember, &$errors) {
                //         if (in_array(strtolower($val['user_email']),  $existTempMember->pluck('user')->pluck('email')->toArray())) {
                //             $errors[] = ['line' => ($k + 2), 'msg' => 'Email already exist in temp member list"' . $val['user_email'] . '"'];
                //         }
                //     });
                //     $existEmail =  $existTempMember->pluck('user')->pluck('email')->toArray();
                //     //Final insert data without existing user
                //     $existUser->each(function ($val, $key) use ($existEmail, $existUser) {
                //         if (in_array(strtolower($val['user_email']), $existEmail)) {
                //             $existUser->forget($key);
                //         }
                //     });
                // }
                //push data workshop meta insert
                $existUser->each(function ($val) use ($insertData, $request, $insertDataDuplicay) {
                    //removing duplicate email from new user collection(SP)
                    if (!$insertData->contains('user_id', $val['user_id'])) {
                        $insertData->push(['workshop_id' => $request->workshop_id, 'user_id' => $val['user_id'], 'fname' => $val['user_first_name'], 'lname' => $val['user_last_name'], 'role' => 0, 'email' => strtolower($val['user_email']), 'created_at' => date('Y-m-d h:i:s')]);
                    } else {
                        $insertDataDuplicay->push($val);

                    }
                });
                $insertDataDuplicay = $insertDataDuplicay->unique('user_email');
                $first = false;
                $recordCollection->each(function ($item, $k) use (&$errors, $insertDataDuplicay, $errorField, $recordCollection, &$first) {
                    if (isset($errorField[$k]) == false) {
                        // var_dump($first,$insertDataDuplicay->contains('user_email',$item['user_email']));
                        if ($insertDataDuplicay->contains('user_email', $item['user_email']) && $first == false) {
                            $first = true;
                        } else {
                            if ($insertDataDuplicay->contains('user_email', $item['user_email']) && $first == true) {
                                $errors[] = ['line' => ($k + 2), 'msg' => 'Duplicate Email  "' . $item['user_email'] . '"'];
                            }
                        }
                    }
                });


            }
            if ($insertData->count() > 0) {
                $emails = $insertData->pluck('email')->toArray();
                $scuess = $insertData->toArray();
                $insertData->transform(function ($i) {
                    unset($i['email']);
                    unset($i['fname']);
                    unset($i['lname']);
                    return $i;
                });
                $metaInsert = WorkshopMetaTemp::insert($insertData->toArray());
//                if ($metaInsert) {
//                    // $dataMail = $this->getMailData($workshop_data, 'commission_new_user');
//                    // $subject = $dataMail['subject'];
//                    // $mailData['mail'] = ['subject' => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'url' => $dataMail['route_members']];
//                    // $this->core->SendMassEmail($mailData, 'new_commission_user');
//                }
            }
            if (count($errors) > 0 && $insertData->count() > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $insertData->count(), 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $insertData->count(), 'status' => 200], 200);
            }
        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
        //code by ravindra
        // insert data in workshopmeta

        // foreach ($records as $k => $val) {
        //     if (isset($errorField[$k]) == false) {
        //         $flag = 0;
        //         if (filter_var($val['user_email'], FILTER_VALIDATE_EMAIL)) {
        //             $user = User::where('email', $val['user_email'])->first(['id', 'fname', 'lname', 'email']);
        //             if (!$user) {
        //                 $newuser = User::create(['fname' => $val['user_first_name'],
        //                     'lname' => $val['user_last_name'],
        //                     'email' => $val['user_email'],
        //                     'password' => Hash::make($val['user_email']),
        //                     'role' => 'M2',
        //                 ]);
        //                 $flag = 1;
        //                 if ($user) {

        //                 }
        //             } else {
        //                 $workshop = WorkshopMeta::where(['user_id' => $newuser->id, 'workshop_id' => $request->workshop_id])->first(['id']);
        //                 if (!$workshop) {
        //                     $flag = 1;
        //                 }
        //             }
        //             if ($flag == 1) {
        //                 WorkshopMeta::create(['workshop_id' => $request->workshop_id,
        //                     'user_id' => $user->id,
        //                     'role' => 0,
        //                 ]);
        //                 $scuess[$count] = $user;
        //                 $count++;
        //             }
        //         } else {
        //             $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid email address "' . $val['user_email'] . '"'];
        //         }
        //     }
        // }

    }

    public function importPastMeeting(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::load($path, function ($reader) {
        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];
        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData->toArray();
            $totalrecord = count($records);
            $error = $this->validationExcel($records, 'past_meeting');
            if (!$this->wrongFileValidation($records[0], 'past_meeting')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }
            $errorField = $error['errorField'];
            $workshop = [];
            $workshop = WorkshopMeta::whereIn('role', array(1, 2))->where('workshop_id', $request->workshop_id)->get();
            $workshopPresident = collect($workshop)->where('role', 1)->first()->toArray();
            $validRecord = collect();
            $recordCollection = collect($records);
            $validator = WorkshopMeta::where('role', 2)->where('workshop_id', $request->workshop_id)->first(['id', 'user_id']);
            $recordCollection->each(function ($item, $k) use (&$errors, $validRecord) {
                if (!checkValidDate($item['date'])) {
                    $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid date "' . $item['date'] . '"'];
                } else {
                    $validRecord->push($item);
                }
            });

            foreach ($validRecord->toArray() as $k => $val) {

                if (isset($errorField[$k]) == false) {

                    $curentDate = Carbon\Carbon::now();
                    if (is_object($val['date'])) {
                        $date = $val['date'];
                    } else {

                        $date = Carbon\Carbon::parse($val['date'] . ' ' . $val['start_time']);
                    }
                    if ($date->ispast()) {
                        $minutes = date('i', strtotime($val['start_time']));
                        $hours = date('H', strtotime($val['start_time']));
                        $minutes = $minutes - ($minutes % 15);
                        $roundStartTime = $hours . ':' . $minutes;
                        $val['start_time'] = date('H:i:s', strtotime($roundStartTime));
                        $minutes = date('i', strtotime($val['end_time']));
                        $hours = date('H', strtotime($val['end_time']));
                        $minutes = $minutes - ($minutes % 15);
                        $roundStartTime = $hours . ':' . $minutes;
                        $val['end_time'] = date('H:i:s', strtotime($roundStartTime));
                        $timestamp = strtotime($val['end_time']);
                        $end_time = date('H:i:s', $timestamp);
                        $data = ['date' => $date->format('Y-m-d'),
                            'name' => $val['name_of_meeting'],
                            'description' => $val['description_of_meeting'],
                            'start_time' => timeConvert($val['start_time'], 'H:i:s'),
                            'end_time' => $end_time,
                            'place' => trim($val['address']),
                            'meeting_type' => 1,
                            'workshop_id' => $request->workshop_id,
                            'status' => 1,
                            'is_prepd_final' => 1,
                            'is_repd_final' => 1,
                            'validated_repd' => 1,
                            'validated_prepd' => 1,
                            'prepd_published_by_user_id' => $validator->user_id,
                            'is_import' => 1,
                            'redacteur' => (isset($workshopPresident['user'])) ? $workshopPresident['user']['fname'] . ' ' . $workshopPresident['user']['lname'] : '',
                            'prepd_published_on' => $curentDate->format('Y-m-d H:i:s'),
                        ];
                        $scuess[$count] = Meeting::create($data);
                        foreach ($workshop as $key => $value) {
                            Presence::create(['workshop_id' => $request->workshop_id,
                                'meeting_id' => $scuess[$count]->id,
                                'user_id' => $value->user_id,
                                'register_status' => ($value->role == 1 || $value->role == 2) ? 'I' : 'NI',
                                'presence_status' => ($value->role == 1 || $value->role == 2) ? 'P' : 'ANE']);
                        }
                        Topic::create([
                            'level' => 1,
                            'topic_title' => 'Cette réunion a été uploadée manuellement',
                            'meeting_id' => $scuess[$count]->id,
                            'workshop_id' => $request->workshop_id,
                            'list_order' => 1,
                            'reuse' => 1,
                        ]);
                        $count++;
                    } else {
                        $errors[] = ['line' => ($k + 2), 'msg' => 'date is not past'];
                    }

                }
            }
            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }

        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function importProjectTask(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $excelData = Excel::load($path, function ($reader) {
        })->get();
        $count = 0;
        $errors = [];
        $scuess = [];

        if (!empty($excelData) && count($excelData) > 0) {
            $records = $excelData->toArray();
            $recordCollection = collect($records);
            $validRecord = collect();
            $totalrecord = count($records);
            $error = $this->validationExcel($records);
            if (!$this->wrongFileValidation($records[0], 'project')) {
                return response()->json(['error' => [['line' => 1, 'msg' => 'Wrong file upload!']], 'status' => 201], 201);
            }
            if (count($error['errors']) > 0) {
                $errors = $error['errors'];
            }
            $errorField = $error['errorField'];
            $recordCollection->each(function ($item, $k) use (&$errors, $validRecord) {
                if (!checkValidDate($item['milestone_start_date'])) {
                    $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid milestone start date "' . $item['milestone_start_date'] . '"'];
                } else if (!checkValidDate($item['milestone_end_date'])) {
                    $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid milestone end date "' . $item['milestone_end_date'] . '"'];
                } else if (!checkValidDate($item['task_start_date'])) {
                    $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid task start date "' . $item['task_start_date'] . '"'];
                } else if (!checkValidDate($item['task_end_date'])) {
                    $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid task end date "' . $item['task_end_date'] . '"'];
                } else {
                    $validRecord->push($item);
                }
            });
            foreach ($validRecord->toArray() as $k => $val) {
                $flag = 0;
                if (isset($errorField[$k]) == false) {
                    $project = Project::where(['project_label' => $val['project_name'], 'wid' => $request->workshop_id])->first(['id']);
                    if (!$project) {
                        $project = Project::create(['project_label' => $val['project_name'],
                            'wid' => $request->workshop_id,
                            'user_id' => $request->user_id,
                            'color_id' => 1]);
                        $flag = 1;
                    } else {
                        $milestone = Milestone::where(['project_id' => $project->id, 'label' => $val['milestone_name']])->first(['id', 'label']);
                        if (!$milestone) {
                            $flag = 1;
                        } else {
                            $flag = 2;
                        }
                    }
                    switch ($flag) {
                        case 1:
                            $milestone = Milestone::create(['project_id' => $project->id,
                                'label' => $val['milestone_name'],
                                'user_id' => $request->user_id,
                                'color_id' => 1,
                                'start_date' => (isset($val['milestone_start_date']) && !empty($val['milestone_start_date'])) ? Carbon\Carbon::parse($val['milestone_start_date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                                'end_date' => Carbon\Carbon::parse($val['milestone_end_date'])->format('Y-m-d'),
                            ]);

                        case 2:

                            $scuess[$count] = Task::create([
                                'workshop_id' => $request->workshop_id,
                                'task_created_by_id' => $request->user_id,
                                'task_text' => $val['task_name'],
                                'milestone_id' => $milestone->id,
                                'start_date' => Carbon\Carbon::parse($val['task_start_date'])->format('Y-m-d'),
                                'end_date' => Carbon\Carbon::parse($val['task_end_date'])->format('Y-m-d'),
                                'assign_for' => 1,
                                'activity_type_id' => 1,
                                'status' => 1,
                                'task_color_id' => 1,
                            ]);
                            if (is_object($scuess[$count]['start_date'])) {
                                $scuess[$count]['start_date'] = $scuess[$count]['start_date']->format('Y-m-d');
                                $scuess[$count]['end_date'] = $scuess[$count]['end_date']->format('Y-m-d');
                            }
                            $scuess[$count]['milestone_id'] = $milestone->label;
                            $scuess[$count]['project_label'] = $val['project_name'];
                            $count++;

                            break;
                    }

                }
            }
            if (count($errors) > 0 && count($scuess) > 0) {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            } else if (count($errors) > 0) {
                return response()->json(['error' => $errors, 'status' => 201], 201);
            } else {
                return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
            }

        } else {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Excel file Empty!']], 'status' => 201], 201);
        }
    }

    public function wrongFileValidation($data, $type)
    {

        $header = [];

        switch ($type) {
            case 'user':

                $header = ['user_firstname', 'user_lastname', 'user_email', 'union_id', 'position_in_union', 'company', 'position_in_company'];
                break;
            case 'union':
                $header = ['family', 'industry', 'union_name', 'union_code', 'union_address1', 'union_zipcode', 'union_city', 'union_country', 'union_phone', 'union_fax', 'contact_email', 'url', 'text_contact_button', 'visible_in_directory', 'logo_file_name', 'description', 'is_internal'];

                break;
            case 'family':
                $header = ['family'];
                break;
            case 'workshop':
                $header = ['workshopname', 'code1', 'code2', 'workshop_description', 'is_visible', 'secretaryfn', 'secretaryln', 'secretaryemail', 'deputyfn', 'deputyln', 'deputyemail'];
                break;
            case 'member':
                $header = ['user_email', 'user_first_name', 'user_last_name'];
                break;
            case 'past_meeting':
                $header = ['name_of_meeting', 'description_of_meeting', 'address', 'date', 'start_time', 'end_time'];
                break;
            case 'project':
                $header = ['project_name', 'milestone_name', 'milestone_start_date', 'milestone_end_date', 'task_name', 'task_start_date', 'task_end_date'];
                break;
            case 'industry':
                $header = ['industry', 'family'];
                break;

        }
        foreach ($data as $key => $value) {
            if (!in_array($key, $header)) {
                return false;
            }
        }
        return true;
    }

    public function importDocument(Request $request)
    {
       
        @ini_set( 'upload_max_size' , '64M' );
        @ini_set( 'post_max_size', '64M');
        @ini_set( 'max_execution_time', '300' );

        $count = 0;
        $errors = [];
        $scuess = [];
        $limitSize = 262144000;
        $path = $request->file('file')->getRealPath();
        $extractPath = public_path() . 'public/temp_uploads';
        $zip = Zip::open($path);

        if (count($zip->listFiles()) == 0) {
            return response()->json(['error' => [['line' => 1, 'msg' => 'Your Zip file Empty!']], 'status' => 201], 201);
        }
        $zip->extract($extractPath);
        $filesArray = [];
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        $Issuer = Issuer::find($request->issuer_id);
        if ($request->workshop_id != '') {
            $workshop = workshop::where('id', $request->workshop_id)->first(['id', 'workshop_name']);
        }
        $workshop_name = $this->core->Unaccent(str_replace(' ', '-', $workshop->workshop_name));
        $filesInFolder = \File::files($extractPath);
        $mountManager = new MountManager([
            's3' => Storage::disk('s3')->getDriver(),
            'local' => Storage::disk('localImportDocument')->getDriver(),
        ]);
        foreach ($filesInFolder as $path) {
            $file = pathinfo($path);

            if (filesize($path) < $limitSize) {
                if (isset($file)) {
                    if (in_array(strtolower($file['extension']), ['doc', 'docx', 'pdf', 'xls', 'png', 'jpg', 'jpeg', 'gif', 'ppt', 'xlsx', 'csv'])) {
                        $folder = $domain . '/' . $workshop_name . '/' . getDocType($request->document_type_id);
                        $filename = hash::make($file['filename']);
                        $upload = ($mountManager->copy('local://' . $file['basename'], 's3://' . $folder . '/' . $filename . '.' . $file['extension']));
                        if ($upload) {
                            $filesArray = ['document_title' => $file['basename'],
                                'document_type_id' => $request->document_type_id,
                                'document_file' => $folder . '/' . $filename . '.' . $file['extension'],
                                'issuer_id' => $request->issuer_id,
                                'increment_number' => getIncrementNumber($request->workshop_id),
                                'workshop_id' => $request->workshop_id,
                            ];
                            $scuess[$count] = RegularDocument::create($filesArray);
                            $scuess[$count]['document_type_id'] = getDocType($request->document_type_id);
                            $scuess[$count]['issuer_id'] = $Issuer->issuer_name;
                            $scuess[$count]['workshop_id'] = $workshop->workshop_name;
                            $count++;
                        }
                    } else {
                        $errors[] = ['msg' => 'Document ‘' . $file['basename'] . '‘ rejected, not allowed format '];
                    }
                }
            } else {
                $errors[] = ['msg' => 'Document ‘' . $file['basename'] . '‘ is too big'];
            }
        }

        $file = new Filesystem;
        $file->cleanDirectory($extractPath);
        return response()->json(['scuess' => $scuess, 'error' => $errors, 'count' => $count, 'status' => 200], 200);
    }

    public function getImportMeetings(Request $request)
    {
        $meetings = Meeting::where(['workshop_id' => $request->workshop_id, 'is_import' => 1])->where([[DB::raw('concat(date," ",start_time)'), '<=', Carbon\Carbon::now('Europe/Paris')->format('Y-m-d H:i:s')]])->orderBy('date', 'desc')->get();
        return response()->json(['meetings' => $meetings, 'status' => 200], 200);
    }

    public function uploadExternalDoc(Request $request)
    {
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        if ($request->workshop_id != '') {
            $workshop = workshop::where('id', $request->workshop_id)->first();
        }
        $workshop_name = $this->core->Unaccent(str_replace(' ', '-', $workshop->workshop_name));
        $folder = $domain . '/' . $workshop_name . '/attendeelist';
        $folder2 = $domain . '/' . $workshop_name . '/REPD';
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        $validator = WorkshopMeta::where('role', 2)->where('workshop_id', $request->workshop_id)->first(['id', 'user_id']);
        if ($request->hasFile('upload_doc')) {
            $meeting_doc = MeetingDocument::where('meeting_id', $request->meeting_id)->first();

            if ($meeting_doc != null) {
                $res = Storage::disk('s3')->delete($meeting_doc->doc_name);
                if ($res) {
                    $del = MeetingDocument::where('meeting_id', $request->meeting_id)->where('workshop_id', $request->workshop_id)->delete();

                }
            }
            $filename = $this->core->fileUploadByS3($request->file('upload_doc'), $folder, 'public');
            $filename2 = $this->core->fileUploadByS3($request->file('upload_doc'), $folder2, 'public');
            $request['doc_name'] = $filename;
            $insertCheck = MeetingDocument::insert($request->except(['upload_doc']));
            $originalFilename = $request->file('upload_doc')->getClientOriginalName();

            $this->repdUPload($request->workshop_id, $request->meeting_id, $validator->user_id, $originalFilename, $filename2);
            if ($insertCheck == true) {
                $curentDate = Carbon\Carbon::now();
                Meeting::where('id', $request->meeting_id)->update(['is_import' => 0,
                    'repd_published_on' => $curentDate->format('Y-m-d H:i:s'),
                    'repd_published_by_user_id' => $validator->user_id,
                    'validated_repd' => 1,
                    'is_repd_final' => 1,
                ]);
                return $this->getImportMeetings($request);
            } else {
                return response()->json(['meetings' => [], 'status' => 201], 201);
            }
        } else {
            return response()->json(['meetings' => [], 'status' => 201], 201);
        }
    }

    public function uploadInternalDoc(Request $request)
    {
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        if ($request->workshop_id != '') {
            $workshop = workshop::where('id', $request->workshop_id)->first();
        }
        $workshop_name = $this->core->Unaccent(str_replace(' ', '-', $workshop->workshop_name));
        $folder = $domain . '/' . $workshop_name . '/attendeelist';
        $folder2 = $domain . '/' . $workshop_name . '/REPD';
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        $validator = WorkshopMeta::where('role', 2)->where('workshop_id', $request->workshop_id)->first(['id', 'user_id']);
        if (isset($request->document_id)) {
            $meeting_doc = MeetingDocument::where('meeting_id', $request->meeting_id)->first();

            if ($meeting_doc != null) {
                $res = Storage::disk('s3')->delete($meeting_doc->doc_name);
                if ($res) {
                    $del = MeetingDocument::where('meeting_id', $request->meeting_id)->where('workshop_id', $request->workshop_id)->delete();

                }
            }
            // $filename = $this->core->fileUploadByS3($request->file('upload_doc'), $folder, 'public');
            $document = RegularDocument::where('id', $request->document_id)->first();
            if ($document) {
                $mountManager = new MountManager([
                    's3' => Storage::disk('s3')->getDriver(),
                    'newS3' => Storage::disk('s3')->getDriver(),

                ]);
                $spliteString = explode('/', $document->document_file);
                $count = count($spliteString);
                $filename = $spliteString[($count - 1)];
                // dd($spliteString,$filename);
                if (!Storage::disk('s3')->exists($folder . '/' . $filename)) {
                    $upload = ($mountManager->copy('s3://' . $document->document_file, 's3://' . $folder . '/' . $filename));
                }

            }
            if (!Storage::disk('s3')->exists($folder2 . '/' . $filename)) {
                $upload2 = ($mountManager->copy('s3://' . $document->document_file, 's3://' . $folder2 . '/' . $filename));
            }

            $request['doc_name'] = $folder . '/' . $filename;
            $insertCheck = MeetingDocument::insert($request->except(['document_id']));
            $request['doc_name'] = $folder2 . '/' . $filename;
            $this->repdUPload($request->workshop_id, $request->meeting_id, $validator->user_id, $filename, $request['doc_name']);
            if ($insertCheck == true) {

                $curentDate = Carbon\Carbon::now();
                Meeting::where('id', $request->meeting_id)->update(['is_import' => 0,
                    'repd_published_on' => $curentDate->format('Y-m-d H:i:s'),
                    'repd_published_by_user_id' => $validator->user_id,
                    'validated_repd' => 1,
                    'is_repd_final' => 1,
                ]);
                return $this->getImportMeetings($request);

            } else {
                return response()->json(['meetings' => [], 'status' => 201], 201);
            }
        } else {
            return response()->json(['meetings' => [], 'status' => 201], 201);
        }
    }

    public function repdUPload($wid, $mid, $validator_id, $filename, $file)
    {
        return RegularDocument::updateOrCreate(['workshop_id' => $wid,
            'event_id' => $mid, 'document_type_id' => 3], [
            'workshop_id' => $wid,
            'event_id' => $mid,
            'created_by_user_id' => $validator_id,
            'issuer_id' => 1,
            'document_type_id' => 3,
            'document_title' => $filename,
            'document_file' => $file,
            'increment_number' => getIncrementNumber($wid),
        ]);
    }

    public function getHostNameData()
    {
        $this->tenancy->website();
        $hostdata = $this->tenancy->hostname();
        $domain = @explode('.' . config('constants.HOST_SUFFIX'), $hostdata->fqdn)[0];
        //$domain = config('constants.HOST_SUFFIX');
        session('hostdata', ['subdomain' => $domain]);
        return $this->tenancy->hostname();
    }

    public function getMailData($workshop_data, $key)
    {
        $currUserFname = Auth::user()->fname;
        $currUserLname = Auth::user()->lname;
        $currUserEmail = Auth::user()->email;
        $settings = getSettingData($key);
        $member = workshopValidatorPresident($workshop_data);
        $keywords = [
            '[[UserFirsrName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
            '[[WorkshopvalidatorFullName]]', '[[ValidatorEmail]]', '[[PresidentEmail]]',
        ];
        $values = [
            $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1, '', '', $member['v']['email'], $member['p']['email'],
        ];

        $subject = (str_replace($keywords, $values, $settings->email_subject));

        $route_members = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/members')]);
        return ['subject' => $subject, 'route_members' => $route_members];
    }

    public function generatePassCode()
    {
        $hostCode = generateRandomValue(3);
        //$hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
        $randCode = generateRandomValue(3);
        $newCode = setPasscode($hostCode, $randCode);
        // $newCode = setPasscode($hostCode->hash, $randCode);
        return $newCode;
    }

    public function getTempUser()
    {
        try {
            $tempUser = Tempusers::orderBy('id', 'desc')->get();
            return response()->json(['data' => $tempUser, 'status' => true]);
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'status' => false]);
        }
    }

    public function getImportEmailAlertUser()
    {
        try {
            $tempUser = User::where('import_email', false)->orderBy('id', 'desc')->get();
            return response()->json(['data' => $tempUser, 'status' => true]);
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'status' => false]);
        }
    }

    public function sendEmailToTempUser(Request $request)
    {
        try {
            $ids = explode(',', $request->ids);
            $error = [];
            $succes = [];
            $successIdUpdate = [];
            $res = 0;
            $users = User::whereIn('id', $ids)->where('import_email', 0)->get();
            $dataMail = $this->meeting->getUserMailData('user_email_setting');
            $route = route(
                'redirect-meeting-view', [
                'userid' => base64_encode(0),
                'type' => 'm',
                'url' => str_rot13('dashboard'),
            ]);
            $subject = utf8_encode($dataMail['subject']);

            foreach ($users as $k=>$val) {
                $mailData['mail'] = ['subject' => $subject, 'email' => strtolower($val->email), 'password' => strtolower($val->email), 'url' => $route];

                $res = $this->core->SendEmail($mailData, 'new_user');
                if ($res) {
                    $successIdUpdate[] = $val->id;
                    $succes[] = $val->email;
                } else {
                    $error[$k] = $val->email;
                }

            }

            $res = User::whereIn('id', $successIdUpdate)
                ->update(['import_email' => 1]);
            if (!empty($succes)) {
                return response()->json(['msg' => 'User email scuessfull', 'erros' => $error, 'scuess' => $succes, 'status' => true], 200);
            } else {
                return response()->json(['msg' => 'User email error', 'erros' => $error, 'scuess' => [], 'status' => false], 200);

            }

        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'status' => false]);
        }
    }

    public function delImportUser(Request $request)
    {
        $ids = explode(',', $request->ids);
        $succes = User::whereIn('id', $ids)->delete();
        if ($succes) {
            return response()->json(['msg' => 'User delete scuessfull', 'status' => 200], 200);
        } else {
            return response()->json(['msg' => 'User delete error', 'status' => 200], 200);
        }
    }

    public function checkValidCombination($data)
    {
//        var_dump($data['code1'], $data['code2']);
        // exit;
        $query = Workshop::where(['code1' => $data['code1'], 'code2' => $data['code2'], 'workshop_type' => 2])->count();

        if ($query > 0) {
            return true;
        } else {
            return false;
        }
    }
}
