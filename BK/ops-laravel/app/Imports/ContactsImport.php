<?php

namespace App\Imports;


use App\Model\ListModel;
    use App\Rules\FrenchName;
use App\Rules\Phone;
use App\Services\ImportServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\Support\Collection;
use Batch;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\Newsletter\Entities\IcontactMeta;
use Modules\Newsletter\Services\IContactSingleton;
use  DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Newsletter\Entities\Contact;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Class ContactsImport
 * @package App\Imports
 */
class ContactsImport implements ToCollection, WithValidation, WithStartRow, WithMultipleSheets
{
    use Importable;

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    protected $listId = 0;
    protected $created = 0;
    protected $updated = 0;
    /**
     * @var array
     */
    protected $ruleArray = [];
    /**
     * @var array
     */
    protected $emails = [];
    /**
     * @var array
     */
    protected $finalData = [];
    /**
     * @var array
     */
    protected $allRules = [];
    /**
     * @var
     */
    protected $data;
    /**
     * @var int
     */
    protected $step = 2;
    protected $entityTypeStr = 'contact';

    /**
     * @param $ruleArray
     */
    public function setRuleArray($ruleArray)
    {
        $this->ruleArray = $ruleArray;
    }

    /**
     * @param $ruleArray
     */
    public function setListID($listId)
    {
        $this->listId = $listId;
    }

    /**
     * @param $key
     * @param $rules
     */
    public function setAllRules($key, $rules)
    {
        $this->allRules[$key] = $rules;
    }

    /**
     * @param $emails
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    /**
     * @param $unique
     */
    public function setFinalData($finalData)
    {
        $this->finalData = $finalData;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $star = '*.';

        $this->allRules[$star . $this->ruleArray['email']] = 'required|email';
            $this->allRules[$star . $this->ruleArray['fname']] = ['required', new FrenchName];
            $this->allRules[$star . $this->ruleArray['lname']] = ['required', new FrenchName];
        isset($this->ruleArray['phone']) ? $this->allRules[$star . $this->ruleArray['phone']] = [
            'nullable',
                new Phone,
        ] : '';
        isset($this->ruleArray['mobile']) ? $this->allRules[$star . $this->ruleArray['mobile']] = [
            'nullable',
            new Phone
        ] : '';
        isset($this->ruleArray['zip_code']) ? $this->allRules[$star . $this->ruleArray['zip_code']] = 'nullable|numeric' : '';
        isset($this->ruleArray['fax']) ? $this->allRules[$star . $this->ruleArray['fax']] = 'nullable|numeric' : '';
        isset($this->ruleArray['entity_website']) ? $this->allRules[$star . $this->ruleArray['entity_website']] = 'nullable|url' : '';
        ksort($this->allRules);
        return $this->allRules;
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        $array = [];
        $star = '*.';
        if (isset($this->ruleArray['fname'])) {
            $array[$star . $this->ruleArray['fname'].'.required'] = __('validation.fNameRequired');
        }
        if (isset($this->ruleArray['lname'])) {
            $array[$star . $this->ruleArray['lname'].'.required'] = __('validation.lNameRequired');
        }
        if (isset($this->ruleArray['company_name'])) {
            $array[$star . $this->ruleArray['company_name'].'.regex'] = __('validation.custom.company.regex');
        }
        if (isset($this->ruleArray['union_name'])) {
            $array[$star . $this->ruleArray['union_name'].'.regex'] = __('validation.custom.company.regex');
        }
        if (isset($this->ruleArray['instance_name'])) {
            $array[$star . $this->ruleArray['instance_name'].'.regex'] = __('validation.custom.company.regex');
        }
//            dd($array);
        return $array;
        return [
            '0' => 'Custom message for :attribute.',
            '1' => 'Custom message for :attribute.',
            '2' => 'Custom message for :attribute.',
        ];
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @return array
     */
    public function customValidationAttributes()
    {
        $star = '*.';
        foreach ($this->ruleArray as $k => $item) {
                if ($item == 10) {
                    $cust[$item] = $k;
                } else {
            $cust[$star . $item] = $k;
        }
            
            }
        return $cust;
        return [
            $star . $this->ruleArray['email'] => 'email',
            $star . $this->ruleArray['fname'] => 'fname',
            $star . $this->ruleArray['lname'] => 'lname',
        ];
    }


    /**
     * @param Collection $rows
     * @throws IlluminateValidationException
     */
    public function collection(Collection $rows)
    {
        $messages = $this->customValidationMessages();
        $attributes = $this->customValidationAttributes();
        $rules = $this->rules();
        try {
            //iterating
            $rows = $rows->map(function ($name) {
                $name[$this->ruleArray['email']] = strtolower($name[$this->ruleArray['email']]);
                return $name;
            });
            $collection = $rows;
            $importService = ImportServices::getInstance();
            $diff = $collection->diffAssoc($rows->unique($this->ruleArray['email']));

            $validator = Validator::make($rows->toArray(), $rules, $messages, $attributes);

            if (count($diff) > 0) {
                $validator->after(function ($validator) use ($diff) {
                    foreach ($diff as $k => $item) {
                        $validator->errors()->add($k, 'email already used.');
                    }
                });
            } else {
                $rowEmails = $collection->pluck($this->ruleArray['email']);
                $existingEmails = $this->emails;
                $contains = $rowEmails->map(function ($value, $key) use ($existingEmails) {

                    if ($existingEmails->pluck('email')->contains(strtolower($value))) {
                        return $key;
                    }
//                    return $existingEmails->pluck('email')->contains(strtolower($value));
                });
                $contains = $contains->filter(function ($value, $key) {
                    if (is_numeric($value)) {
                        return $value >= 0;
                    }
                })->toArray();

            }

            $validator->validate();
                $custom = $default = $entity = $entityPos = $custom = $entityPosType = [];
                $ins = $cusIns = $eniIns = $entityPosIns = $entityPosTypeIns = $containsIns = $cusContainsIns = $eniContainsIns = $entityPosContainsIns = $entityPosTypeContainsIns = [];
            if ($this->step == 2) {
                foreach ($this->finalData as $item) {

                    if (isset($item['map']) && isset($item['is_custom'])) {
                        $arrKey = array_keys($item['map']);
                        if ($item['tab'] == 'personal_tab') {
                            $default[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                        }
                        if ($item['tab'] == 'professional_tab') {

                                if ($item['map'][$arrKey[0]] == 'company_position' || $item['map'][$arrKey[0]] == 'instance_position' || $item['map'][$arrKey[0]] == 'union_position' || $item['map'][$arrKey[0]] == 'press_position') {
                                $entityPos[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                                } elseif ($item['map'][$arrKey[0]] == 'membership_type') {
                                    $entityPosType[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                            } else {
                                $entity[__('message.' . $item['map'][$arrKey[0]])] = $arrKey[0];
                            }
                        }
                        if ($item['is_custom']) {
                            $custom[isset($item['map'][$arrKey[0]]['label']) ? $item['map'][$arrKey[0]]['label'] : 0] = $arrKey[0];
                        }
                    }
                }

                foreach ($rows as $key => $row) {
                    if ($key <= 2) {
                        foreach ($default as $k => $item) {

                            $show[$key][$k] = strtolower($row[$item]);
                        }
                        foreach ($custom as $k => $item1) {
                            $show[$key][$k] = $row[$item1];
                        }
                        foreach ($entity as $k => $item2) {
                            $show[$key][$k] = $row[$item2];
                        }

                        foreach ($entityPos as $k => $item3) {
                            $show[$key][$k] = $row[$item3];
                            }
                            foreach ($entityPosType as $k => $item4) {
                                $show[$key][$k] = $row[$item3];
                        }
                    }

                }
//                foreach ($rows as $k => $row) {
//                    if ($k <= 2) {
//                        $arr[] = $row;
//                    }
//                }
                $this->setData($show);

            }

            if ($this->step == 3) {
                $entity = [];
                foreach ($this->finalData as $item) {

                    if (isset($item['map']) && isset($item['is_custom'])) {
                        $arrKey = array_keys($item['map']);
                        if ($item['tab'] == 'personal_tab') {
                            $default[$item['map'][$arrKey[0]]] = $arrKey[0];
                        }
                        if ($item['tab'] == 'professional_tab') {

                                if ($item['map'][$arrKey[0]] == 'company_position' || $item['map'][$arrKey[0]] == 'instance_position' || $item['map'][$arrKey[0]] == 'union_position' || $item['map'][$arrKey[0]] == 'press_position') {
                                $entityPos[$item['map'][$arrKey[0]]] = $arrKey[0];
                                } elseif ($item['map'][$arrKey[0]] == 'membership_type') {
                                    
                                    $entityPosType[$item['map'][$arrKey[0]]] = $arrKey[0];
                            } else {
                                $entity[$item['map'][$arrKey[0]]] = $arrKey[0];
                            }
                        }
                        if ($item['is_custom']) {

                            $custom[$arrKey[0]] = [isset($item['map'][$arrKey[0]]['value']) ? $item['map'][$arrKey[0]]['value'] : 0, isset($item['map'][$arrKey[0]]['skill_format_id']) ? $item['map'][$arrKey[0]]['skill_format_id'] : 0];
                        }
                    }
                }

                foreach ($rows as $key => $row) {
                        if (in_array($key, $contains, TRUE)) {
                        foreach ($default as $k => $item) {
                            if ($k == 'email') {
//                                $containsIns[$key][$k] = strtolower($row[$item]);
                            } else {
                                $containsIns[$key][$k] = $row[$item];
                            }
                        }
                        foreach ($custom as $k => $item1) {
                            $cusContainsIns[$key][$item1[0]] = ['value' => $row[$k], 'skill_format_id' => $item1[1]];
                        }
                        foreach ($entity as $k => $item2) {
                            $eniContainsIns[$key][$k] = $row[$item2];
                        }
                        foreach ($entityPos as $k => $item3) {
                            $entityPosContainsIns[$key][$k] = $row[$item3];
                        }
                            foreach ($entityPosType as $k => $item4) {
                                $entityPosTypeContainsIns[$key][$k] = $row[$item3];
                            }
                    } else {
                        foreach ($default as $k => $item) {
                            if ($k == 'email') {
                                $ins[$key][$k] = $this->Unaccent(strtolower($row[$item]));
                                $ins[$key]['created_at'] = Carbon::now()->format('Y-m-d');
                            } else {
                                $ins[$key][$k] = $row[$item];
                            }
                        }
                        foreach ($custom as $k => $item1) {
                            $cusIns[$key][$item1[0]] = ['value' => $row[$k], 'skill_format_id' => $item1[1]];
                        }
                        foreach ($entity as $k => $item2) {

                            $eniIns[$key][$k] = $row[$item2];
                        }
                        foreach ($entityPos as $k => $item3) {
                            $entityPosIns[$key][$k] = $row[$item3];
                        }
                            
                            foreach ($entityPosType as $k => $item4) {
                                $entityPosTypeIns[$key][$k] = $row[$item3];
                    }
                }
                    }
               
                    DB::transaction(function () use ($ins, $cusIns, $importService, $eniIns, $entityPosIns, $containsIns, $cusContainsIns, $eniContainsIns, $entityPosContainsIns, $contains, $rows, $entityPosTypeIns, $entityPosTypeContainsIns, $entityPosType) {
                    $className = new Contact();
                    $lastIdBeforeInsertion = $importService->getLastId($className);
//                    dd($lastIdBeforeInsertion,$containsIns, $cusContainsIns, $eniContainsIns, $entityPosContainsIns);
                    // 2- insert your data
                    Contact::insert($ins);
                    if (count($contains) > 0) {
                        foreach ($contains as $contain) {
                            $email = $rows[$contain][$this->ruleArray['email']];
                            $kId = $this->emails->search(function ($value, $key) use ($email) {
                                return (strtolower($value->email) == strtolower($email));
                            });

                            if (isset($this->emails[$kId]->id)) {
                                $cusContainsIns[$contain]['id'] = $this->emails[$kId]->id;
                                $eniContainsIns[$contain]['user_id'] = $this->emails[$kId]->id;
//                                $entityPosContainsIns[$contain]['id'] = $this->emails[$kId]->id;
                                $userIds[] = $this->emails[$kId]->id;
                                $containsIns[$contain]['id'] = $this->emails[$kId]->id;
//                                unset($containsIns[$contain]['email']);
                            }
                        }
                        $userInstance = new Contact;
                        $index = 'id';
                        Batch::update($userInstance, $containsIns, $index);
                    }

                    // 3- Getting the last inserted ids
                    $insertedIds = [];

                    for ($i = 1, $iMax = count($ins); $i <= $iMax; $i++) {
                        array_push($insertedIds, $lastIdBeforeInsertion + $i);
                    }

                    if ($this->listId > 0) {
                        $list = ListModel::find($this->listId, ['type', 'id']);
                        if ($list->type == 0) {
                            $list = ListModel::find(($this->listId - 1), ['type', 'id']);
                        }

                        //here we are adding these users to Icontact as a Contact
                        $iContact = IContactSingleton::getInstance();
                        $ids = [];
                        foreach ($ins as $i => $in) {
                            $ids[] = ['email' => $in['email'], 'firstName' => $in['fname'], 'lastName' => $in['lname'], 'ops_id' => ($insertedIds[$i] - 1)];
                        }
                        $count = count($ids);
                        $checkAlready = IcontactMeta::whereIn('column_id', array_column($containsIns, 'id'))->where('type', 1)->get(['column_id', 'icontact_id', 'created_at']);
                        $already = [];

                        foreach ($containsIns as $k => $item) {

                            if (!in_array($item['id'], $checkAlready->pluck('column_id')->toArray())) {
                                $ids[($count + $k)] = ['email' => $item['email'], 'firstName' => $item['fname'], 'lastName' => $item['lname'], 'ops_id' => $item['id']];
                            } else {
                                $already[] = $item['id'];
                            }
                        }

                        //this is because list id is internal
                        $icontactListId = IcontactMeta::where('column_id', $list->id)->where('type', 2)->first(['icontact_id']);
                        if (count($ids) > 0) {
                            $iContacts = $iContact->createContact($ids);
                            if (isset($iContacts) && (count($iContacts->contacts) > 0)) {
                                $listAll = ListModel::where('type', 4)->first();
                                $icontactListAllId = IcontactMeta::where('column_id', $listAll->id)->where('type', 2)->first(['icontact_id']);

                                //we need to update this custom field id later
                                foreach ($iContacts->contacts as $iContact) {
                                    $icontactMeta[] = [
                                        'type' => 1,
                                        'column_id' => $iContact->ops_id,
                                        'icontact_id' => $iContact->contactId,
                                        'created_at' => $iContact->createDate,
                                    ];
                                    $subscribe[] = [
                                        "listId" => $icontactListId->icontact_id,
                                        "contactId" => $iContact->contactId,
                                        "status" => "normal"
                                    ];
                                    $subscribeAll[] = [
                                        "listId" => $icontactListAllId->icontact_id,
                                        "contactId" => $iContact->contactId,
                                        "status" => "normal"
                                    ];
                                    $attach[] = $iContact->ops_id;
                                }

                                //Inserting data in Meta table
                                IcontactMeta::insert($icontactMeta);
                                //adding users to list in iContact
                                $addSub = new IContactSingleton;
                                $addSub->addSubscriber($subscribe);
                                $addSub->addSubscriber($subscribeAll);

                                //adding the same relationship in db (list and contact)
                                $this->attachList($list, $attach);
                                $this->attachList($listAll, $attach);

                            }
                        }
                        if (count($already) > 0) {
                            $checkInListAlready = $list->newsletter_contacts;
                            //we need to update this custom field id later
                            $listAll = ListModel::where('type', 4)->first();
                            $icontactListAllId = IcontactMeta::where('column_id', $listAll->id)->where('type', 2)->first(['icontact_id']);

                            foreach ($already as $iContact) {
                                //
                                $key = array_search($iContact, $checkAlready->pluck('column_id')->toArray());

                                if ($key !== false && (!in_array($iContact, $checkInListAlready->pluck('id')->toArray()))) {
                                    $subscribe[] = [
                                        "listId" => $icontactListId->icontact_id,
                                        "contactId" => $checkAlready[$key]->icontact_id,
                                        "status" => "normal"
                                    ];
                                    $subscribeAll[] = [
                                        "listId" => $icontactListAllId->icontact_id,
                                        "contactId" => $checkAlready[$key]->icontact_id,
                                        "status" => "normal"
                                    ];
                                    $attach[] = $iContact;
                                }
                            }

                            //adding users to list in iContact
                            $addSub = new IContactSingleton;
                            if (isset($subscribe)) {
                                $addSub->addSubscriber($subscribe);
                            }
                            if (isset($subscribeAll)) {
                                $addSub->addSubscriber($subscribeAll);
                            }
                            //adding the same relationship in db (list and contact)
                            if (isset($attach)) {
                                $this->attachList($list, $attach);
                                $this->attachList($listAll, $attach);
                            }
                        }

                    }

                    if (count($cusIns) > 0 || $cusContainsIns > 0) {
                        $importService->addUserSkill($insertedIds, $cusIns, $this->entityTypeStr, $cusContainsIns);
                    }

                    if (count($eniIns) > 0 || count($eniContainsIns) > 0) {
                            $importService->addPersonEntity($insertedIds, $eniIns, 1, $entityPosIns, $eniContainsIns, $entityPosContainsIns, $entityPosTypeIns, $entityPosTypeContainsIns, $entityPosType);
                    }
                    $this->created = count($insertedIds);
                    $this->updated = count($containsIns);
                });

            }

        } catch (IlluminateValidationException $e) {
            throw new IlluminateValidationException(
                $e->errors(),
                $attributes
            );
        }

    }

    public function attachList($list, $attach)
    {
        if ($list->type) {
            return $list->newsletter_contacts()->attach($attach);
        }
        return $list->users()->attach($attach);
    }

    function Unaccent($string)
    {
            $unwanted_array = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', '÷' => '', 'ü' => 'u', '\'' => ''];
        $str = strtr($string, $unwanted_array);

        return $str;
    }
}
